<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;

/**
 * @deprecated
 */
class Nots implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed|int|float|string|bool $operand,
        public bool                                $doubled = false,
    ) {
    }
}
