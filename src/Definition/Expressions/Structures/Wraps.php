<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class Wraps implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed|int|float|string|bool $wrappedOperand,
    ) {
    }
}
