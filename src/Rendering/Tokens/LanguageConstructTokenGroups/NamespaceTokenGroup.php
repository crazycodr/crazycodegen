<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NamespaceToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\NamespacePathToken;

class NamespaceTokenGroup extends TokenGroup
{
    use FlattenFunction;

    public function __construct(
        public readonly string $path,
    ) {
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
        $tokens[] = new SpacesToken($rules->namespaces->spacesAfterNamespace);
        $tokens[] = new NamespacePathToken($this->path);
        $tokens[] = new SemiColonToken();
        $tokens[] = new NewLineTokens($rules->namespaces->newLinesAfterSemiColon);
        return $this->flatten($tokens);
    }
}
