<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;

class NullVal extends BaseVal
{
    public function __construct()
    {
    }

    public function getTokens(RenderingContext $context): array
    {
        return [new NullToken()];
    }
}
