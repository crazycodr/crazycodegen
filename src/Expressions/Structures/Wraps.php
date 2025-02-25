<?php

namespace CrazyCodeGen\Expressions\Structures;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Wraps implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $wrappedOperand,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten(['(', $this->wrappedOperand->getTokens(), ')']);
    }
}