<?php

namespace CrazyCodeGen\Definitions\Values;

use CrazyCodeGen\Base\CanBeComputed;

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
