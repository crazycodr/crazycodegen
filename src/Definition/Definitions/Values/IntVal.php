<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\Token;

class IntVal extends BaseVal
{
    public function __construct(
        public int $value,
    ) {
    }

    public function getTokens(RenderingContext $context): array
    {
        return [new Token($this->value)];
    }
}
