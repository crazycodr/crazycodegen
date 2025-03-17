<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesClassType;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
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
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassDef extends Tokenizes implements ProvidesClassType, ProvidesClassReference, ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use ValidationTrait;

    /**
     * @throws InvalidIdentifierFormatException
     */
    public function __construct(
        public string                        $name,
        public null|string|NamespaceDef      $namespace = null,
        /** @var string[]|ClassTypeDef[]|ImportDef[] $imports */
        public array                         $imports = [],
        public null|string|array|DocBlockDef $docBlock = null,
        public bool                          $abstract = false,
        public null|string|ClassTypeDef      $extends = null,
        /** @var string[]|ClassTypeDef[] $implements */
        public array                         $implements = [],
        /** @var string[]|PropertyDef[] $properties */
        public array                         $properties = [],
        /** @var MethodDef[] $methods */
        public array                         $methods = [],
    ) {
        $this->setNamespace($namespace);
        $this->imports = $this->convertAndDropNonCompliantValues($this->imports, [
            new ConversionRule(inputType: 'string', outputType: ImportDef::class),
            new ConversionRule(inputType: ClassTypeDef::class, outputType: ImportDef::class, propertyPaths: ['type']),
            new ConversionRule(inputType: ImportDef::class),
        ]);
        $this->setDocBlock($docBlock);
        $this->setName($name);
        $this->setExtends($extends);
        $this->implements = $this->convertAndDropNonCompliantValues($this->implements, [
            new ConversionRule(inputType: 'string', outputType: ClassTypeDef::class),
            new ConversionRule(inputType: ClassTypeDef::class),
        ]);
        $this->properties = $this->convertAndDropNonCompliantValues($this->properties, [
            new ConversionRule(inputType: 'string', outputType: PropertyDef::class),
            new ConversionRule(inputType: PropertyDef::class),
        ]);
        $this->methods = $this->convertAndDropNonCompliantValues($this->methods, [
            new ConversionRule(inputType: 'string', outputType: MethodDef::class),
            new ConversionRule(inputType: MethodDef::class),
        ]);
    }

    public function setNamespace(null|string|NamespaceDef $namespace): self
    {
        if (is_string($namespace)) {
            $namespace = new NamespaceDef($namespace);
        }
        $this->namespace = $namespace;
        return $this;
    }

    public function addImport(string|ClassTypeDef|ImportDef $import): self
    {
        if (is_string($import)) {
            $this->imports[] = new ImportDef($import);
        } elseif ($import instanceof ClassTypeDef) {
            $this->imports[] = new ImportDef($import->type);
        }
        return $this;
    }

    /**
     * @param string|string[]|DocBlockDef $docBlock
     * @return $this
     */
    public function setDocBlock(null|string|array|DocBlockDef $docBlock): self
    {
        if (is_string($docBlock)) {
            $docBlock = new DocBlockDef([$docBlock]);
        } elseif (is_array($docBlock)) {
            $docBlock = array_filter($docBlock, fn ($value) => is_string($value));
            $docBlock = new DocBlockDef($docBlock);
        }
        $this->docBlock = $docBlock;
        return $this;
    }

    /**
     * @throws InvalidIdentifierFormatException
     */
    public function setName(string $name): self
    {
        $this->assertIsValidIdentifier($name);
        $this->name = $name;
        return $this;
    }

    public function setExtends(null|string|ClassTypeDef $extends): self
    {
        if (is_string($extends)) {
            $extends = new ClassTypeDef($extends);
        }
        $this->extends = $extends;
        return $this;
    }

    public function addProperty(string|PropertyDef $property): self
    {
        if (is_string($property)) {
            $property = new PropertyDef($property);
        }
        $this->properties[] = $property;
        return $this;
    }

    public function addMethod(string|MethodDef $method): self
    {
        if (is_string($method)) {
            $method = new MethodDef($method);
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
            $tokens[] = $this->extends->getTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = $this->namespace->getTokens($context, $rules);
        }

        $previousImportedClasses = $context->importedClasses;
        $importsLeft = count($this->imports);
        foreach ($this->imports as $import) {
            $importsLeft--;
            $tokens[] = $import->getTokens($context, $rules);
            if (!$import->alias) {
                $context->importedClasses[] = $import->type->type;
            }
            if ($importsLeft > 0) {
                $tokens[] = new NewLinesToken($rules->classes->newLinesAfterEachImport);
            }
        }
        if (!empty($this->imports)) {
            $tokens[] = new NewLinesToken($rules->classes->newLinesAfterAllImports);
        }

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context, $rules);
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
            $tokens[] = $this->insertIndentationTokens($rules, $property->getTokens($context, $rules));
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
            $tokens[] = $this->insertIndentationTokens($rules, $method->getTokens($context, $rules));
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
    ): array {
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
        $implementsTokenGroup = new ImplementsDef($this->implements);
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
        $implementsTokenGroup = new ImplementsDef($this->implements);
        return $implementsTokenGroup->renderChopDownScenario($context, $rules);
    }

    public function getClassReference(): ClassRefVal
    {
        return new ClassRefVal($this->getClassType());
    }

    public function getClassType(): ClassTypeDef
    {
        if ($this->namespace) {
            return new ClassTypeDef($this->namespace->path . '\\' . $this->name);
        }
        return new ClassTypeDef($this->name);
    }

    public function getCallableReference(): Tokenizes
    {
        return $this->getClassType()->getCallableReference();
    }
}
