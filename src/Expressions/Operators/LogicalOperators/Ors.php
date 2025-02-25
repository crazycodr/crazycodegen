<?php

namespace CrazyCodeGen\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Ors implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
        public bool          $textBased = false,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), $this->textBased ? 'or' : '||', $this->right->getTokens()]);
    }
}