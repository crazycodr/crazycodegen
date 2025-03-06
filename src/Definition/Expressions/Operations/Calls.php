<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\CallableTrait;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

class Calls implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;
    use CallableTrait;

    public function __construct(
        public CanBeCalled|string $target,
        public array              $arguments = [],
    ) {
    }
}
