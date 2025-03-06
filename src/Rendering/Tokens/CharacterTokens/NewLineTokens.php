<?php

namespace CrazyCodeGen\Rendering\Tokens\CharacterTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class NewLineTokens extends Token
{
    public function __construct(int $times = 1)
    {
        parent::__construct(str_repeat("\n", $times));
    }
}