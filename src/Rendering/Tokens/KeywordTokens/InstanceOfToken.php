<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class InstanceOfToken extends Token
{
    public function __construct()
    {
        parent::__construct('instanceof');
    }
}
