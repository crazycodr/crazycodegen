<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class UseToken extends Token
{
    public function __construct()
    {
        parent::__construct('use');
    }
}