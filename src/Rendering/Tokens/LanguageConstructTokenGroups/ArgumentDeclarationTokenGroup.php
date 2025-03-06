<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;

class ArgumentDeclarationTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use RenderTokensToStringTrait;

    public function __construct(
        public readonly string|IdentifierToken             $name,
        public readonly null|string|AbstractTypeTokenGroup $type = null,
        public readonly null|int|float|string|bool|Token   $defaultValue = null,
        public readonly bool                               $defaultValueIsNull = false,
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
        $tokens = array_merge($tokens, $typeTokens = $this->renderType($context, $rules));
        $tokens = array_merge($tokens, $this->getSpacesBetweenTypesAndIdentifier($typeTokens, $context, $rules));
        $tokens = array_merge($tokens, $identifierTokens = $this->renderIdentifier($context, $rules));
        $tokens = array_merge($tokens, $this->renderDefaultValue($context, $rules, $identifierTokens));
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderType(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (is_string($this->type)) {
            $tokens[] = (new SingleTypeTokenGroup(type: $this->type))->render($context, $rules);
        } elseif (!is_null($this->type)) {
            $tokens[] = $this->type->render($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderIdentifier(RenderContext $context, RenderingRules $rules): array
    {
        return $this->flatten((new VariableTokenGroup($this->name))->render($context, $rules));
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
        if ($this->defaultValueIsNull) {
            $tokens[] = $this->getSpacesBetweenIdentifierAndEquals($identifierTokens, $context, $rules);
            $tokens[] = new EqualToken();
            $tokens[] = $this->getSpacesBetweenEqualsAndValue($rules);
            $tokens[] = new NullToken();
        } elseif ($this->defaultValue) {
            $tokens[] = $this->getSpacesBetweenIdentifierAndEquals($identifierTokens, $context, $rules);
            $tokens[] = new EqualToken();
            $tokens[] = $this->getSpacesBetweenEqualsAndValue($rules);
            if (is_string($this->defaultValue)) {
                $tokens[] = (new StringTokenGroup($this->defaultValue))->render($context, $rules);
            } elseif (is_bool($this->defaultValue)) {
                $tokens[] = new Token($this->defaultValue ? 'true' : 'false');
            } else {
                $tokens[] = new Token($this->defaultValue);
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::argument,
        ], parent::getContexts());
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
            $spacesToAdd = $rules->argumentDefinitionRenderingRules->spacesBetweenArgumentTypeAndIdentifier;
        } else {
            return [];
        }
        return [new SpacesToken($spacesToAdd)];
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
            $spacesToAdd = $rules->argumentDefinitionRenderingRules->spacesBetweenArgumentIdentifierAndEquals;
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
        return [new SpacesToken($rules->argumentDefinitionRenderingRules->spacesBetweenArgumentEqualsAndValue)];
    }
}