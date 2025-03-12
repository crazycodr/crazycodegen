<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class IssetToken extends Token
{
    public function __construct()
    {
        parent::__construct('isset');
    }
}
