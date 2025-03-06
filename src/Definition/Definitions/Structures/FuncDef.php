<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class FuncDef implements CanBeComputed, CanBeCalled
{
    use FlattenFunction;

    public function __construct(
        public string      $name,
        /** @var ArgDef[] $arguments */
        public array       $arguments = [],
        public null|string $returnType = null,
        public null|string $namespace = null,
        public array       $bodyInstructions = [],
    ) {
    }

    public function getCallReference(): string
    {
        return $this->name;
    }
}
