<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class FunctionToken extends Token
{
    public function __construct()
    {
        parent::__construct('function');
    }
}