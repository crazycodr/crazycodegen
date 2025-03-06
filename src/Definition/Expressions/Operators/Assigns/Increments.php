<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assigns;

use CrazyCodeGen\Definition\Base\CanBeAssigned;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

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