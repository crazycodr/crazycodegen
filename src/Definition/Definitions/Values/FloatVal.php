<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\Token;

class FloatVal extends BaseVal
{
    public function __construct(
        public float $value,
    ) {
    }

    public function getSimpleTokens(TokenizationContext $context): array
    {
        return [new Token($this->value)];
    }
}
