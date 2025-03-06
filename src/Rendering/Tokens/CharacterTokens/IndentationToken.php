<?php

namespace CrazyCodeGen\Rendering\Tokens\CharacterTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class IndentationToken extends Token
{
    public function __construct(string $indentation)
    {
        parent::__construct($indentation);
    }
}