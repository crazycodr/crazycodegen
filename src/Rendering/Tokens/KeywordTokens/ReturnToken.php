<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class ReturnToken extends Token
{
    public function __construct()
    {
        parent::__construct('return');
    }
}
