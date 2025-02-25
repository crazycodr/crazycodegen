<?php

namespace CrazyCodeGen\Expressions\Operators\Comparisons;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class IsLessThanOrEqualTo implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), '<=', $this->right->getTokens()]);
    }
}