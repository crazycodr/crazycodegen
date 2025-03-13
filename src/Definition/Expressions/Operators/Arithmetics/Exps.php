<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Arithmetics;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class Exps implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed|int|float|string|bool $left,
        public CanBeComputed|int|float|string|bool $right,
    ) {
    }
}
