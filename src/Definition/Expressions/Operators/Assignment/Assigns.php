<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assignment;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeAssigned;
use CrazyCodeGen\Definition\Base\CanBeComputed;

/**
 * @deprecated
 */
class Assigns implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeAssigned                       $left,
        public CanBeComputed|int|float|string|bool $right,
    ) {
    }
}
