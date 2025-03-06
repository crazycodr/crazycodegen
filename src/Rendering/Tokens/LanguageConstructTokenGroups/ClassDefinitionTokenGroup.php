<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ExtendsToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;

class ClassDefinitionTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use RenderTokensToStringTrait;

    public function __construct(
        public readonly string|IdentifierToken             $name,
        public readonly null|NamespaceTokenGroup           $namespace = null,
        /** @var string[]|ImportTokenGroup[] $imports */
        public readonly array                              $imports = [],
        public readonly null|string|DocBlockTokenGroup     $docBlock = null,
        public readonly bool                               $abstract = false,
        public readonly null|string|AbstractTypeTokenGroup $extends = null,
        /** @var string[]|AbstractTypeTokenGroup[] $implements */
        public readonly array                              $implements = [],
        /** @var PropertyTokenGroup[] $properties */
        public readonly array                              $properties = [],
        /** @var MethodDefinitionTokenGroup[] $methods */
        public readonly array                              $methods = [],
    )
    {
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
            if (is_string($import)) {
                $tokens[] = (new ImportTokenGroup($import))->render($context, $rules);
                $context->importedClasses[] = $import;
            } else {
                $tokens[] = $import->render($context, $rules);
                if (!$import->alias) {
                    $context->importedClasses[] = $import->type;
                }
            }
            if ($importsLeft > 0) {
                $tokens[] = new NewLineTokens($rules->classes->newLinesBetweenImports);
            }
        }
        if (!empty($this->imports)) {
            $tokens[] = new NewLineTokens($rules->classes->newLinesAfterAllImports);
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->render($context, $rules);
            $tokens[] = new NewLineTokens($rules->classes->linesAfterDocBlock);
        }

        $scenarios = [];
        /** @var array{extends: WrappingDecision, implements: WrappingDecision, individualImplements: WrappingDecision} $permutation */
        foreach ($this->getWrapRulePermutations($rules) as $permutation) {
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
        $tokens = array_merge($tokens, array_pop($scenarios));

        if ($rules->classes->classOpeningBrace === BracePositionEnum::SAME_LINE) {
            $tokens[] = new SpacesToken($rules->classes->spacesBeforeOpeningBraceIfSameLine);
            $tokens[] = new BraceStartToken();
        } else {
            $tokens[] = new NewLineTokens();
            $tokens[] = SpacesToken::fromString($context->indents);
            $tokens[] = new BraceStartToken();
        }
        $rules->indent($context);
        if (!empty($this->properties) || !empty($this->methods)) {
            $tokens[] = new NewLineTokens();
        }

        $propertiesLeft = count($this->properties);
        foreach ($this->properties as $property) {
            $propertiesLeft--;
            $tokens[] = $this->insertIndentationTokens($context, $property->render($context, $rules));
            if ($propertiesLeft > 0) {
                $tokens[] = new NewLineTokens($rules->classes->newLinesBetweenProperties);
            }
        }

        if (!empty($this->properties) && !empty($this->methods)) {
            $tokens[] = new NewLineTokens($rules->classes->newLinesBetweenPropertiesAndMethods);
        }

        $methodsLeft = count($this->methods);
        foreach ($this->methods as $method) {
            $methodsLeft--;
            $tokens[] = $this->insertIndentationTokens($context, $method->render($context, $rules));
            if ($methodsLeft > 0) {
                $tokens[] = new NewLineTokens($rules->classes->newLinesBetweenMethods);
            }
        }

        if ($rules->classes->classClosingBrace !== BracePositionEnum::SAME_LINE) {
            $rules->unindent($context);
            $tokens[] = new NewLineTokens();
            $tokens[] = SpacesToken::fromString($context->indents);
        }
        $tokens[] = new BraceEndToken();
        $tokens[] = new NewLineTokens($rules->classes->newLinesAfterClosingBrace);

        $context->importedClasses = $previousImportedClasses;

        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::function,
        ], parent::getContexts());
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
        if (!$this->name instanceof IdentifierToken) {
            $tokens[] = new IdentifierToken($this->name);
        } else {
            $tokens[] = $this->name;
        }
        return $this->flatten($tokens);
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
            if (!$this->extends instanceof AbstractTypeTokenGroup) {
                $tokens[] = (new SingleTypeTokenGroup($this->extends))->render($context, $rules);
            } else {
                $tokens[] = $this->extends->render($context, $rules);
            }
        }
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
        $implementsTokenGroup = new ImplementsTokenGroup($this->implements);
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
        $implementsTokenGroup = new ImplementsTokenGroup($this->implements);
        return $implementsTokenGroup->renderChopDownScenario($context, $rules);
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
            $scenario[] = new NewLineTokens();
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
            $scenario[] = new NewLineTokens();
            $rules->indent($context);
            $scenario[] = $this->insertIndentationTokens($context, $inlineImplementsTokens);
            $rules->unindent($context);
        } elseif (!empty($chopDownImplementsTokens)) {
            $scenario[] = new NewLineTokens();
            $rules->indent($context);
            $scenario[] = $this->insertIndentationTokens($context, $chopDownImplementsTokens);
            $rules->unindent($context);
        }
        return $this->flatten($scenario);
    }

    /**
     * @param Token[] $declarationTokens
     * @param Token[] $extendsTokens
     * @param Token[] $inlineImplementsTokens
     * @return Token[]
     */
    public function getInlineExtendsImplementsNextLineDeclaration(
        array $declarationTokens,
        array $extendsTokens,
        array $inlineImplementsTokens
    ): array
    {
        $scenario = [];
        $scenario[] = $declarationTokens;
        $scenario[] = new SpacesToken();
        $scenario[] = $extendsTokens;
        $scenario[] = new SpacesToken();
        $scenario[] = $inlineImplementsTokens;
        return $this->flatten($scenario);
    }

    /**
     * @param RenderContext $context
     * @param Token[] $tokens
     * @return Token[]
     */
    private function insertIndentationTokens(RenderContext $context, array $tokens): array
    {
        if (strlen($context->indents) === 0) {
            return $tokens;
        }
        $newTokens = [];
        $newTokens[] = SpacesToken::fromString($context->indents);
        foreach ($tokens as $token) {
            $newTokens[] = $token;
            if ($token instanceof NewLineTokens) {
                $newTokens[] = SpacesToken::fromString($context->indents);
            }
        }
        return $newTokens;
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
}