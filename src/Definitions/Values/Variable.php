<?php

namespace CrazyCodeGen\Definitions\Values;

use CrazyCodeGen\Base\CanBeAssigned;
use CrazyCodeGen\Base\CanBeComputed;

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
