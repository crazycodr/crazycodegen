<?php

namespace CrazyCodeGen\Definitions\Values;

use CrazyCodeGen\Base\CanBeComputed;

class BoolValue implements CanBeComputed
{
    public function __construct(
        public bool $value,
    ) {
    }

    public function getTokens(): array
    {
        return [$this->value ? 'true' : 'false'];
    }
}
