<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class IterableToken extends Token
{
    public function __construct()
    {
        parent::__construct('iterable');
    }
}
