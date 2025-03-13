<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Comparisons;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class IsLessThan implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed|int|float|string|bool $left,
        public CanBeComputed|int|float|string|bool $right,
    ) {
    }
}
