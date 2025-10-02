<?php

namespace CrazyCodeGen\Rendering\Tokens\CharacterTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class SpacesToken extends Token
{
    public function __construct(int $size = 1)
    {
        parent::__construct(str_repeat(' ', $size));
    }
}
