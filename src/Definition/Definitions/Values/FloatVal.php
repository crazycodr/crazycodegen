<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\Token;

class FloatVal extends BaseVal
{
    public function __construct(
        public float $value,
    ) {
    }

    public function getTokens(RenderingContext $context): array
    {
        return [new Token((string)$this->value)];
    }
}
