<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

class FloatValue implements CanBeComputed
{
    public function __construct(
        public float $value,
    )
    {
    }

    public function getTokens(): array
    {
        return [$this->value];
    }
}
