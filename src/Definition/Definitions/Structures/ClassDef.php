<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class ClassDef implements CanBeComputed, CanBeCalled
{
    use FlattenFunction;

    public function __construct(
        public string      $name,
        public null|string $extends = null,
        public null|VisibilityEnum $visibility = null,
        public bool $abstract = false,
        public array $implements = [],
        public null|string $namespace = null,
    )
    {
    }

    public function getCallReference(): string
    {
        return $this->name;
    }
}
