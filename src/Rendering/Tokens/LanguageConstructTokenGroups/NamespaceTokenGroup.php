<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NamespaceToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\NamespacePathToken;

class NamespaceTokenGroup extends TokenGroup
{
    use FlattenFunction;

    public function __construct(
        public readonly string $path,
        public readonly null|string $alias = null
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
        $tokens[] = new NamespaceToken();
        $tokens[] = new SpacesToken();
        $tokens[] = new NamespacePathToken($this->path);
        if ($this->alias) {
            $tokens[] = new SpacesToken();
            $tokens[] = new AsToken();
            $tokens[] = new SpacesToken();
            $tokens[] = new IdentifierToken($this->alias);
        }
        $tokens[] = new SemiColonToken();
        $tokens[] = $this->repeatNTimes($rules->newLinesAfterNamespace, new NewLineToken());
        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::namespace,
        ], parent::getContexts());
    }
}