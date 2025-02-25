<?php

namespace CrazyCodeGen\Expressions\Operators\Strings;

use CrazyCodeGen\Base\CanBeAssigned;
use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

class ConcatAssigns implements CanBeComputed
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
            '.=',
            $this->makeComputed($this->right)->getTokens()
        ]);
    }
}