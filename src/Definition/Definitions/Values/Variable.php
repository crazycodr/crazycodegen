<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeAssigned;
use CrazyCodeGen\Definition\Base\CanBeComputed;

class Variable implements CanBeAssigned, CanBeComputed
{
    public function __construct(
        public string $name,
    )
    {
    }

    public function getTokens(): array
    {
        return ['$', $this->name];
    }
}
