<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\Token;

abstract class Tokenizes
{
    /**
     * Returns all tokens in a minimal serial way to be formatted by an external formatter.
     * @param RenderingContext $context
     * @return Token[]
     */
    abstract public function getTokens(RenderingContext $context): array;
}
