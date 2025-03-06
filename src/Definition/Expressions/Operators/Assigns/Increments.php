<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assigns;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeAssigned;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class Increments implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeAssigned $operand,
        public bool          $pre = false,
    ) {
    }
}
