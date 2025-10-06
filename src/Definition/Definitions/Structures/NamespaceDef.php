<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NamespaceToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class NamespaceDef extends Tokenizes
{
    use FlattenFunction;

    public function __construct(
        public string $path,
    ) {
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new NamespaceToken();
        $tokens[] = new SpacesToken();
        $tokens[] = new Token($this->path);
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
