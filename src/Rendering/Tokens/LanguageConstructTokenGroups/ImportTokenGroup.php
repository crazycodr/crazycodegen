<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\UseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;

class ImportTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use RenderTokensToStringTrait;

    public function __construct(
        public string|SingleTypeTokenGroup $type,
        public null|string                 $alias = null,
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
        $tokens[] = new UseToken();
        $tokens[] = new SpacesToken($rules->imports->spacesAfterUse);
        if (is_string($this->type)) {
            $tokens[] = (new SingleTypeTokenGroup($this->type))->render($context, $rules);
        } else {
            $tokens[] = $this->type->render($context, $rules);
        }
        if ($this->alias) {
            $tokens[] = new SpacesToken($rules->imports->spacesAfterType);
            $tokens[] = new AsToken();
            $tokens[] = new SpacesToken($rules->imports->spacesAfterAs);
            $tokens[] = new IdentifierToken($this->alias);
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::argumentList,
        ], parent::getContexts());
    }
}