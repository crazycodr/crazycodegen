<?php

namespace CrazyCodeGen\Expressions\Operators\Comparisons;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Equals implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
        public bool          $soft = false,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), $this->soft ? '==' : '===', $this->right->getTokens()]);
    }
}