<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assigns;

use CrazyCodeGen\Definition\Base\CanBeAssigned;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class Assigns implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeAssigned                       $left,
        public CanBeComputed|int|float|string|bool $right,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            $this->left->getTokens(),
            '=',
            $this->makeComputed($this->right)->getTokens()
        ]);
    }
}