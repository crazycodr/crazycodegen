<?php

namespace CrazyCodeGen\Rendering\Tokens\CharacterTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class SemiColonToken extends Token
{
    public function __construct()
    {
        parent::__construct(';');
    }
}
