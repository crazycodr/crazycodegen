<?php

namespace CrazyCodeGen\Rendering\Renderers;

use CrazyCodeGen\Rendering\Tokens\Token;

trait RenderTokensToStringTrait
{
    /**
     * @param Token[] $tokens
     * @return string
     */
    private function renderTokensToString(array $tokens): string
    {
        return join('', array_map(fn(Token $token) => $token->render(), $tokens));
    }
}