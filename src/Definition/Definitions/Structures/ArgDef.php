<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Base\CanBeAssigned;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

class ArgDef implements CanBeAssigned, CanBeComputed
{
    use ComputableTrait;

    public function __construct(
        public string      $name,
        public null|string $type = null,
        public null|string $defaultValue = null,
        public bool        $defaultValueIsNull = false,
    ) {
    }
}
