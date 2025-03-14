<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ExpansionToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ParameterDef extends Tokenizes implements ProvidesVariableReference, ProvidesCallableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;
    use ValueInferenceTrait;

    public const UNSET_DEFAULT_VALUE = '@!#UNSET@!#';

    public function __construct(
        public string        $name,
        public null|string|TypeDef $type = null,
        public mixed               $defaultValue = self::UNSET_DEFAULT_VALUE,
        public bool                $isVariadic = false,
    ) {
        if (is_string($this->type)) {
            $this->type = $this->inferType($this->type);
        }
        if ($this->defaultValue === self::UNSET_DEFAULT_VALUE) {
            // Do nothing or isSupportedValue will change to StringVal
        } elseif ($this->isInferableValue($this->defaultValue)) {
            $this->defaultValue = $this->inferValue($this->defaultValue);
        } else {
            $this->defaultValue = self::UNSET_DEFAULT_VALUE;
        }
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = $typeTokens = $this->renderType($context, $rules);
        $tokens[] = $this->getSpacesBetweenTypesAndIdentifier($typeTokens, $context, $rules);
        $tokens[] = $identifierTokens = $this->renderIdentifier($context, $rules);
        $tokens[] = $this->renderDefaultValue($context, $rules, $identifierTokens);
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderType(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->type) {
            $tokens[] = $this->type->getTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @param Token[] $typesTokens
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    private function getSpacesBetweenTypesAndIdentifier(array $typesTokens, RenderContext $context, RenderingRules $rules): array
    {
        if ($context->chopDown?->paddingSpacesForTypes) {
            $tokensAsString = $this->renderTokensToString($typesTokens);
            $spacesToAdd = max(0, $context->chopDown->paddingSpacesForTypes - strlen($tokensAsString)) + 1;
        } elseif (!empty($typesTokens)) {
            $spacesToAdd = $rules->parameters->spacesAfterType;
        } else {
            return [];
        }
        return [new SpacesToken($spacesToAdd)];
    }

    /**
     * @return Token[]
     */
    public function renderIdentifier(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->isVariadic) {
            $tokens[] = new ExpansionToken();
        }
        $tokens[] = (new VariableDef($this->name))->getTokens($context, $rules);
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param Token[] $identifierTokens
     * @return Token[]
     */
    public function renderDefaultValue(RenderContext $context, RenderingRules $rules, array $identifierTokens): array
    {
        $tokens = [];
        if ($this->defaultValue !== self::UNSET_DEFAULT_VALUE) {
            $tokens[] = $this->getSpacesBetweenIdentifierAndEquals($identifierTokens, $context, $rules);
            $tokens[] = new EqualToken();
            $tokens[] = $this->getSpacesBetweenEqualsAndValue($rules);
            $tokens[] = $this->defaultValue->getTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @param Token[] $identifierTokens
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    private function getSpacesBetweenIdentifierAndEquals(array $identifierTokens, RenderContext $context, RenderingRules $rules): array
    {
        if ($context->chopDown?->paddingSpacesForIdentifiers) {
            $tokensAsString = $this->renderTokensToString($identifierTokens);
            $spacesToAdd = max(0, $context->chopDown->paddingSpacesForIdentifiers - strlen($tokensAsString)) + 1;
        } elseif (!empty($identifierTokens)) {
            $spacesToAdd = $rules->parameters->spacesAfterIdentifier;
        } else {
            return [];
        }
        return [new SpacesToken($spacesToAdd)];
    }

    /**
     * @param RenderingRules $rules
     * @return Token[]
     */
    private function getSpacesBetweenEqualsAndValue(RenderingRules $rules): array
    {
        return [new SpacesToken($rules->parameters->spacesAfterEquals)];
    }

    public function getVariableReference(): VariableDef
    {
        return new VariableDef($this->name);
    }

    public function getCallableReference(): Tokenizes
    {
        return $this->getVariableReference();
    }
}
