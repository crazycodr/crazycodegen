<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class CallableToken extends Token
{
    public function __construct()
    {
        parent::__construct('callable');
    }
}
