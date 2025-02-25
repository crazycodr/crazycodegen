<?php

namespace CrazyCodeGen\Definitions\Values;

use CrazyCodeGen\Base\CanBeComputed;

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
