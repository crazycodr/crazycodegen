<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class ElseToken extends Token
{
    public function __construct()
    {
        parent::__construct('else');
    }
}