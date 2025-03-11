<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ExtendsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassDefinition extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string                               $name,
        public null|string|NamespaceDefinition      $namespace = null,
        /** @var string[]|SingleTypeDefinition[]|MultiTypeDefinition[]|ImportDefinition[] $imports */
        public array                                $imports = [],
        public null|string|array|DocBlockDefinition $docBlock = null,
        public bool                                 $abstract = false,
        public null|string|SingleTypeDefinition     $extends = null,
        /** @var string[]|SingleTypeDefinition[] $implements */
        public array                                $implements = [],
        /** @var string[]|PropertyDefinition[] $properties */
        public array                                $properties = [],
        /** @var MethodDefinition[] $methods */
        public array                                $methods = [],
    )
    {
        $this->setNamespace($namespace);
        foreach ($this->imports as $importIndex => $import) {
            if (is_string($import)) {
                $this->imports[$importIndex] = new ImportDefinition($import);
            } elseif ($import instanceof SingleTypeDefinition) {
                $this->imports[$importIndex] = new ImportDefinition($import->type);
            } elseif ($import instanceof MultiTypeDefinition) {
                foreach ($import->getAllTypes() as $type) {
                    $this->imports[] = new ImportDefinition($type);
                }
                unset($this->imports[$importIndex]);
            } elseif (!$import instanceof ImportDefinition) {
                unset($this->imports[$importIndex]);
            }
        }
        $this->setDocBlock($docBlock);
        $this->setExtends($extends);
        foreach ($this->implements as $implementIndex => $implement) {
            if (is_string($implement)) {
                $this->implements[$implementIndex] = new SingleTypeDefinition($implement);
            } elseif (!$implement instanceof SingleTypeDefinition) {
                unset($this->implements[$implementIndex]);
            }
        }
        foreach ($this->properties as $propertyIndex => $property) {
            if (is_string($property)) {
                $this->properties[$propertyIndex] = new PropertyDefinition($property);
            } elseif (!$property instanceof PropertyDefinition) {
                unset($this->properties[$propertyIndex]);
            }
        }
        foreach ($this->methods as $methodIndex => $method) {
            if (is_string($method)) {
                $this->methods[$methodIndex] = new MethodDefinition($method);
            } elseif (!$method instanceof MethodDefinition) {
                unset($this->methods[$methodIndex]);
            }
        }
    }

    public function setNamespace(null|string|NamespaceDefinition $namespace): self
    {
        if (is_string($namespace)) {
            $namespace = new NamespaceDefinition($namespace);
        }
        $this->namespace = $namespace;
        return $this;
    }

    public function addImport(string|SingleTypeDefinition|MultiTypeDefinition|ImportDefinition $import): self
    {
        if (is_string($import)) {
            $this->imports[] = new ImportDefinition($import);
        } elseif ($import instanceof SingleTypeDefinition) {
            $this->imports[] = new ImportDefinition($import->type);
        } elseif ($import instanceof MultiTypeDefinition) {
            foreach ($import->getAllTypes() as $type) {
                $this->imports[] = new ImportDefinition($type);
            }
        }
        return $this;
    }

    /**
     * @param string|string[]|DocBlockDefinition $docBlock
     * @return $this
     */
    public function setDocBlock(null|string|array|DocBlockDefinition $docBlock): self
    {
        if (is_string($docBlock)) {
            $docBlock = new DocBlockDefinition([$docBlock]);
        } elseif (is_array($docBlock)) {
            $docBlock = array_filter($docBlock, fn($value) => is_string($value));
            $docBlock = new DocBlockDefinition($docBlock);
        }
        $this->docBlock = $docBlock;
        return $this;
    }

    public function setExtends(null|string|SingleTypeDefinition $extends): self
    {
        if (is_string($extends)) {
            $extends = new SingleTypeDefinition($extends);
        }
        $this->extends = $extends;
        return $this;
    }

    public function addProperty(string|PropertyDefinition $property): self
    {
        if (is_string($property)) {
            $property = new PropertyDefinition($property);
        }
        $this->properties[] = $property;
        return $this;
    }

    public function addMethod(string|MethodDefinition $method): self
    {
        if (is_string($method)) {
            $method = new MethodDefinition($method);
        }
        $this->methods[] = $method;
        return $this;
    }

    /**
     * @return Token[]
     */
    private function getExtendsTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->extends) {
            $tokens[] = new ExtendsToken();
            $tokens[] = new SpacesToken();
            $tokens[] = $this->extends->render($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->render($context, $rules);
        }

        $previousImportedClasses = $context->importedClasses;
        $importsLeft = count($this->imports);
        foreach ($this->imports as $import) {
            $importsLeft--;
            $tokens[] = $import->render($context, $rules);
            if (!$import->alias) {
                $context->importedClasses[] = $import->type;
            }
            if ($importsLeft > 0) {
                $tokens[] = new NewLinesToken($rules->classes->newLinesAfterEachImport);
            }
        }
        if (!empty($this->imports)) {
            $tokens[] = new NewLinesToken($rules->classes->newLinesAfterAllImports);
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->render($context, $rules);
            $tokens[] = new NewLinesToken($rules->classes->newLinesAfterDocBlock);
        }

        $scenarios = [];
        foreach ($this->getWrapRulePermutations($rules) as $permutation) {
            /** @var array{extends: WrappingDecision, implements: WrappingDecision, individualImplements: WrappingDecision} $permutation */
            $scenario = $this->getDeclarationByWrappingPermutation(
                $permutation['extends'],
                $permutation['implements'],
                $permutation['individualImplements'],
                $context,
                $rules,
            );
            $textScenario = $this->renderTokensToString($scenario);
            $textScenarioLines = explode("\n", $textScenario);
            foreach ($textScenarioLines as $textScenarioLine) {
                if ($rules->exceedsAvailableSpace($context->getCurrentLine(), $textScenarioLine)) {
                    if (
                        str_contains($textScenarioLine, 'extends ')
                        && $rules->classes->extendsOnNextLine === WrappingDecision::NEVER
                    ) {
                        continue;
                    }
                    if (
                        str_contains($textScenarioLine, 'implements ')
                        && (
                            $rules->classes->implementsOnNextLine === WrappingDecision::NEVER
                            || $rules->classes->implementsOnDifferentLines === WrappingDecision::NEVER
                        )
                    ) {
                        continue;
                    }
                    continue 2;
                }
            }
            $scenarios[] = $scenario;
        }

        /** @var Token[] $tokens */
        $tokens = array_merge($tokens, reset($scenarios));

        if ($rules->classes->openingBrace === BracePositionEnum::SAME_LINE) {
            $tokens[] = new SpacesToken($rules->classes->spacesBeforeOpeningBrace);
        } else {
            $tokens[] = new NewLinesToken();
            $tokens[] = SpacesToken::fromString($context->indents);
        }
        $tokens[] = new BraceStartToken();
        $rules->indent($context);
        if (!empty($this->properties) || !empty($this->methods)) {
            $tokens[] = new NewLinesToken();
        }

        $propertiesLeft = count($this->properties);
        foreach ($this->properties as $property) {
            $propertiesLeft--;
            $tokens[] = $this->insertIndentationTokens($rules, $property->render($context, $rules));
            if ($propertiesLeft > 0) {
                $tokens[] = new NewLinesToken($rules->classes->newLinesAfterEachProperty);
            }
        }

        if (!empty($this->properties) && !empty($this->methods)) {
            $tokens[] = new NewLinesToken($rules->classes->newLinesAfterProperties);
        }

        $methodsLeft = count($this->methods);
        foreach ($this->methods as $method) {
            $methodsLeft--;
            $tokens[] = $this->insertIndentationTokens($rules, $method->render($context, $rules));
            if ($methodsLeft > 0) {
                $tokens[] = new NewLinesToken($rules->classes->newLinesAfterEachMethod);
            }
        }

        if ($rules->classes->closingBrace !== BracePositionEnum::SAME_LINE) {
            $rules->unindent($context);
            $tokens[] = new NewLinesToken();
            $tokens[] = SpacesToken::fromString($context->indents);
        }
        $tokens[] = new BraceEndToken();
        $tokens[] = new NewLinesToken($rules->classes->newLinesAfterClosingBrace);

        $context->importedClasses = $previousImportedClasses;

        return $this->flatten($tokens);
    }

    /**
     * @param RenderingRules $rules
     * @return array<array{extends: WrappingDecision, implements: WrappingDecision, individualImplements: WrappingDecision}>
     */
    private function getWrapRulePermutations(RenderingRules $rules): array
    {
        $permutations = [];

        $extendsChopWrapDecisions = [];
        if ($rules->classes->extendsOnNextLine === WrappingDecision::NEVER) {
            $extendsChopWrapDecisions[] = WrappingDecision::NEVER;
        } elseif ($rules->classes->extendsOnNextLine === WrappingDecision::IF_TOO_LONG) {
            $extendsChopWrapDecisions[] = WrappingDecision::NEVER;
            $extendsChopWrapDecisions[] = WrappingDecision::ALWAYS;
        } elseif ($rules->classes->extendsOnNextLine === WrappingDecision::ALWAYS) {
            $extendsChopWrapDecisions[] = WrappingDecision::ALWAYS;
        }

        $implementsChopWrapDecisions = [];
        if ($rules->classes->implementsOnNextLine === WrappingDecision::NEVER) {
            $implementsChopWrapDecisions[] = WrappingDecision::NEVER;
        } elseif ($rules->classes->implementsOnNextLine === WrappingDecision::IF_TOO_LONG) {
            $implementsChopWrapDecisions[] = WrappingDecision::NEVER;
            $implementsChopWrapDecisions[] = WrappingDecision::ALWAYS;
        } elseif ($rules->classes->implementsOnNextLine === WrappingDecision::ALWAYS) {
            $implementsChopWrapDecisions[] = WrappingDecision::ALWAYS;
        }

        $individualImplementsChopWrapDecisions = [];
        if (in_array(WrappingDecision::ALWAYS, $implementsChopWrapDecisions)) {
            if ($rules->classes->implementsOnDifferentLines === WrappingDecision::NEVER) {
                $individualImplementsChopWrapDecisions[WrappingDecision::NEVER->name] = WrappingDecision::NEVER;
            } elseif ($rules->classes->implementsOnDifferentLines === WrappingDecision::IF_TOO_LONG) {
                $individualImplementsChopWrapDecisions[WrappingDecision::NEVER->name] = WrappingDecision::NEVER;
                $individualImplementsChopWrapDecisions[WrappingDecision::ALWAYS->name] = WrappingDecision::ALWAYS;
            } elseif ($rules->classes->implementsOnDifferentLines === WrappingDecision::ALWAYS) {
                $individualImplementsChopWrapDecisions[WrappingDecision::ALWAYS->name] = WrappingDecision::ALWAYS;
            }
        }
        if (in_array(WrappingDecision::NEVER, $implementsChopWrapDecisions)) {
            $individualImplementsChopWrapDecisions[WrappingDecision::NEVER->name] = WrappingDecision::NEVER;
        }

        foreach ($extendsChopWrapDecisions as $extendsChopWrapDecision) {
            foreach ($implementsChopWrapDecisions as $implementsChopWrapDecision) {
                foreach ($individualImplementsChopWrapDecisions as $individualImplementsChopWrapDecision) {
                    $permutations[] = [
                        'extends' => $extendsChopWrapDecision,
                        'implements' => $implementsChopWrapDecision,
                        'individualImplements' => $individualImplementsChopWrapDecision,
                    ];
                }
            }
        }

        return $permutations;
    }

    /**
     * @return Token[]
     */
    public function getDeclarationByWrappingPermutation(
        WrappingDecision $wrapExtends,
        WrappingDecision $wrapImplements,
        WrappingDecision $wrapIndividualImplements,
        RenderContext    $context,
        RenderingRules   $rules,
    ): array
    {
        $scenario = [];
        $scenario[] = $this->getDeclarationTokens();
        $extendsTokens = $this->getExtendsTokens($context, $rules);
        $inlineImplementsTokens = $this->getInlineImplementsTokens($context, $rules);
        $chopDownImplementsTokens = $this->getChopDownImplementsTokens($context, $rules);
        if ($wrapExtends === WrappingDecision::NEVER && !empty($extendsTokens)) {
            $scenario[] = new SpacesToken();
            $scenario[] = $extendsTokens;
        } elseif (!empty($extendsTokens)) {
            $scenario[] = new NewLinesToken();
            $rules->indent($context);
            $scenario[] = SpacesToken::fromString($context->indents);
            $scenario[] = $extendsTokens;
            $rules->unindent($context);
        }
        if ($wrapImplements === WrappingDecision::NEVER && !empty($inlineImplementsTokens)) {
            $scenario[] = new SpacesToken();
            $scenario[] = $inlineImplementsTokens;
        } elseif (
            ($wrapImplements === WrappingDecision::ALWAYS || $wrapImplements === WrappingDecision::IF_TOO_LONG)
            && $wrapIndividualImplements === WrappingDecision::NEVER
            && !empty($inlineImplementsTokens)
        ) {
            $scenario[] = new NewLinesToken();
            $rules->indent($context);
            $scenario[] = $this->insertIndentationTokens($rules, $inlineImplementsTokens);
            $rules->unindent($context);
        } elseif (!empty($chopDownImplementsTokens)) {
            $scenario[] = new NewLinesToken();
            $rules->indent($context);
            $scenario[] = $this->insertIndentationTokens($rules, $chopDownImplementsTokens);
            $rules->unindent($context);
        }
        return $this->flatten($scenario);
    }

    /**
     * @return Token[]
     */
    private function getDeclarationTokens(): array
    {
        $tokens = [];
        if ($this->abstract) {
            $tokens[] = new AbstractToken();
            $tokens[] = new SpacesToken();
        }
        $tokens[] = new ClassToken();
        $tokens[] = new SpacesToken();
        $tokens[] = new Token($this->name);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    private function getInlineImplementsTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (empty($this->implements)) {
            return $tokens;
        }
        $implementsTokenGroup = new ImplementsDefinition($this->implements);
        return $implementsTokenGroup->renderInlineScenario($context, $rules);
    }

    /**
     * @return Token[]
     */
    private function getChopDownImplementsTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (empty($this->implements)) {
            return $tokens;
        }
        $implementsTokenGroup = new ImplementsDefinition($this->implements);
        return $implementsTokenGroup->renderChopDownScenario($context, $rules);
    }
}
