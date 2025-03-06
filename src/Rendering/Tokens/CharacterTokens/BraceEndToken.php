<?php

namespace CrazyCodeGen\Rendering\Tokens\CharacterTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class BraceEndToken extends Token
{
    public function __construct()
    {
        parent::__construct('}');
    }
}