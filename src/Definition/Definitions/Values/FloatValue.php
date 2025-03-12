<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

/**
 * @deprecated
 */
class FloatValue implements CanBeComputed
{
    public function __construct(
        public float $value,
    ) {
    }
}
