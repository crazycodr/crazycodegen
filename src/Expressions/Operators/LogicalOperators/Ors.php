<?php

namespace CrazyCodeGen\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

class Ors implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeComputed|int|float|string|bool $left,
        public CanBeComputed|int|float|string|bool $right,
        public bool                                $textBased = false,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            $this->makeComputed($this->left)->getTokens(),
            $this->textBased ? 'or' : '||',
            $this->makeComputed($this->right)->getTokens()
        ]);
    }
}