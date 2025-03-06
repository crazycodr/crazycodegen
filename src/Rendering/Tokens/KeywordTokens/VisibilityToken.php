<?php

namespace CrazyCodeGen\Rendering\Tokens\KeywordTokens;

use CrazyCodeGen\Definitions\Structures\VisibilityEnum;
use CrazyCodeGen\Rendering\Tokens\Token;

class VisibilityToken extends Token
{
    public function __construct(VisibilityEnum $visibility)
    {
        parent::__construct($visibility->value);
    }
}