<?php

namespace CrazyCodeGen\Expressions\Operators\Comparisons;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class NotEquals implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
        public bool          $soft = false,
        public bool          $useLtGt = false
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), $this->useLtGt ? '<>' : ($this->soft ? '!=' : '!=='), $this->right->getTokens()]);
    }
}