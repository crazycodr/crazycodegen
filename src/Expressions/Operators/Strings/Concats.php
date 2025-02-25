<?php

namespace CrazyCodeGen\Expressions\Operators\Strings;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\FlattenFunction;

class Concats implements CanBeComputed
{
    use FlattenFunction;

    public function __construct(
        public CanBeComputed $left,
        public CanBeComputed $right,
    ) {
    }

    public function getTokens(): array
    {
        return $this->flatten([$this->left->getTokens(), '.', $this->right->getTokens()]);
    }
}