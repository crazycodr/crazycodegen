<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;

class NullVal extends BaseVal
{
    public function __construct()
    {
    }

    public function getSimpleTokens(TokenizationContext $context): array
    {
        return [new NullToken()];
    }
}
