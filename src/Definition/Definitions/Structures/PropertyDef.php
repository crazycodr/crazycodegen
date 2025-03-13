<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StaticToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\VisibilityToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class PropertyDef extends Tokenizes implements ProvidesVariableReference
{
    use FlattenFunction;
    use TokenFunctions;
    use TypeInferenceTrait;
    use ValueInferenceTrait;

    public const UNSET_DEFAULT_VALUE = '@!#UNSET@!#';

    public function __construct(
        public string|Token            $name,
        public null|string|DocBlockDef $docBlock = null,
        public null|string|TypeDef     $type = null,
        public VisibilityEnum          $visibility = VisibilityEnum::PUBLIC,
        public bool                    $static = false,
        public mixed                   $defaultValue = self::UNSET_DEFAULT_VALUE,
    ) {
        if (is_string($this->type)) {
            $this->type = $this->inferType($this->type);
        }
        if ($this->defaultValue === self::UNSET_DEFAULT_VALUE) {
            // Do nothing or isSupportedValue will change to StringVal
        } elseif ($this->isSupportedValue($this->defaultValue)) {
            $this->defaultValue = $this->inferValue($this->defaultValue);
        } elseif ($this->defaultValue instanceof ProvidesClassReference) {
            $this->defaultValue = $this->defaultValue->getClassReference();
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

        if ($this->docBlock) {
            $tokens[] = $this->docBlock->getTokens($context, $rules);
            $tokens[] = new NewLinesToken($rules->properties->newLinesAfterDocBlock);
        }

        $tokens[] = $this->renderVisibility($context, $rules);
        $tokens[] = $this->renderModifiers($context, $rules);
        $tokens[] = $this->renderType($context, $rules);
        $tokens[] = $this->renderIdentifier($context, $rules);
        $tokens[] = $this->renderDefaultValue($context, $rules);
        $tokens[] = new SemicolonToken();
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderVisibility(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = $tokenToPad = new VisibilityToken($this->visibility);
        $tokens[] = new SpacesToken($this->calculatePaddingOrGetRuleSpaces(
            $tokenToPad,
            $context->chopDown?->paddingSpacesForVisibilities,
            $rules->properties->spacesAfterVisibility
        ));
        return $this->flatten($tokens);
    }

    /**
     * @param Token[]|Token $tokensToPad
     * @param int|null $paddingContextSpaces
     * @param int $ruleSpacingValue
     * @return int
     */
    private function calculatePaddingOrGetRuleSpaces(
        array|Token $tokensToPad,
        ?int        $paddingContextSpaces,
        int         $ruleSpacingValue
    ): int {
        if ($paddingContextSpaces) {
            $renderedTokensLength = strlen($this->renderTokensToString(
                is_array($tokensToPad) ? $tokensToPad : [$tokensToPad]
            ));
            return max(1, $paddingContextSpaces - $renderedTokensLength);
        }
        return $ruleSpacingValue;
    }

    /**
     * @return Token[]
     */
    public function renderModifiers(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->static) {
            $tokens[] = $tokenToPad = new StaticToken();
            $tokens[] = new SpacesToken($this->calculatePaddingOrGetRuleSpaces(
                $tokenToPad,
                $context->chopDown?->paddingSpacesForModifiers,
                $rules->properties->spacesAfterStatic
            ));
        } else {
            $tokens[] = new SpacesToken($context->chopDown?->paddingSpacesForModifiers ?? 0);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderType(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (!is_null($this->type)) {
            $tokens[] = $tokensToPad = $this->type->getTokens($context, $rules);
            $tokens[] = new SpacesToken($this->calculatePaddingOrGetRuleSpaces(
                $tokensToPad,
                $context->chopDown?->paddingSpacesForTypes,
                $rules->properties->spacesAfterType
            ));
        } else {
            $tokens[] = new SpacesToken($context->chopDown?->paddingSpacesForTypes ?? 0);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderIdentifier(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = $tokensToPad = (new VariableDef($this->name))->getTokens($context, $rules);
        if ($this->defaultValue !== self::UNSET_DEFAULT_VALUE) {
            $tokens[] = new SpacesToken($this->calculatePaddingOrGetRuleSpaces(
                $tokensToPad,
                $context->chopDown?->paddingSpacesForIdentifiers,
                $rules->properties->spacesAfterIdentifier
            ));
        }
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderDefaultValue(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->defaultValue !== self::UNSET_DEFAULT_VALUE) {
            $tokens[] = new EqualToken();
            $tokens[] = new SpacesToken($rules->properties->spacesAfterEquals);
            $tokens[] = $this->defaultValue->getTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }

    public function getVariableReference(): VariableDef
    {
        return new VariableDef($this->name);
    }
}
