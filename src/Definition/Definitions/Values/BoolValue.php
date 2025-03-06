<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

class BoolValue implements CanBeComputed
{
    public function __construct(
        public bool $value,
    ) {
    }
}
