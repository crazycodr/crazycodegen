<?php

namespace CrazyCodeGen\Rendering\Tokens\UserLandTokens;

use CrazyCodeGen\Rendering\Tokens\Token;

class NamespacePathToken extends Token
{
    public function __construct(string $identifier)
    {
        parent::__construct($identifier);
    }
}
