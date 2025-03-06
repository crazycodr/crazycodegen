<?php

namespace CrazyCodeGen\Rendering\Tokens\CharacterTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class NewLineToken extends Token
{
    public function __construct()
    {
        parent::__construct("\n");
    }
}