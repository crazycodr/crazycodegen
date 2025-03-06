<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class VariableTokenGroup extends TokenGroup
{
    use FlattenFunction;

    public function __construct(
        public readonly string|IdentifierToken $name,
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
        $tokens[] = new DollarToken();
        if (is_string($this->name)) {
            $tokens[] = new IdentifierToken($this->name);
        } else {
            $tokens[] = $this->name;
        }
        if ($context->argumentDefinitionIdentifierPaddingSize > 0) {
            $tokens[] = new SpacesToken($context->argumentDefinitionIdentifierPaddingSize);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::identifier,
        ], parent::getContexts());
    }
}