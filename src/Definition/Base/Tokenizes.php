<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\Token;

abstract class Tokenizes
{
    /**
     * Returns all tokens in a minimal serial way to be formatted by an external formatter.
     * @param TokenizationContext $context
     * @return Token[]
     */
    abstract public function getSimpleTokens(TokenizationContext $context): array;
}
