<?php

namespace CrazyCodeGen\Expressions\Operators\Arithmetics;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Adds implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), '+', $this->right->getTokens()]);
    }
}