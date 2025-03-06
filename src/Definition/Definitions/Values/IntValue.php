<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

class IntValue implements CanBeComputed
{
    public function __construct(
        public int $value,
    )
    {
    }

    public function getTokens(): array
    {
        return [$this->value];
    }
}
