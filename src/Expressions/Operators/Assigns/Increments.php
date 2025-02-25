<?php

namespace CrazyCodeGen\Expressions\Operators\Assigns;

use CrazyCodeGen\Base\CanBeAssigned;
use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Increments implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeAssigned $operand,
        public bool          $pre = false,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->pre ? '++' : [], $this->operand->getTokens(), $this->pre ? [] : '++']);
    }
}