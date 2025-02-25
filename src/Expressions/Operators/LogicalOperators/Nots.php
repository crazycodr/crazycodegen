<?php

namespace CrazyCodeGen\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Nots implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $operand,
        public bool          $doubled = false,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->doubled ? ['!', '!'] : ['!'], $this->operand->getTokens()]);
    }
}