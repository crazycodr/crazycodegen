<?php

namespace CrazyCodeGen\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Xors implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), 'xor', $this->right->getTokens()]);
    }
}