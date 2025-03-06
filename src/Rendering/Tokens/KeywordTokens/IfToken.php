<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class IfToken extends Token
{
    public function __construct()
    {
        parent::__construct('if');
    }
}