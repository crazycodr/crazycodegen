<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class ParentToken extends Token
{
    public function __construct()
    {
        parent::__construct('parent');
    }
}
