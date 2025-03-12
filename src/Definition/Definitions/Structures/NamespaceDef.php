<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Defines;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NamespaceToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class NamespaceDef extends Defines
{
    use FlattenFunction;

    public function __construct(
        public string $path,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new NamespaceToken();
        $tokens[] = new SpacesToken($rules->namespaces->spacesAfterNamespace);
        $tokens[] = new Token($this->path);
        $tokens[] = new SemiColonToken();
        $tokens[] = new NewLinesToken($rules->namespaces->newLinesAfterSemiColon);
        return $this->flatten($tokens);
    }
}
