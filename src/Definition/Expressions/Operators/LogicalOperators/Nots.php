<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class Nots implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeComputed|int|float|string|bool $operand,
        public bool                                $doubled = false,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            $this->doubled ? ['!', '!'] : ['!'],
            $this->makeComputed($this->operand)->getTokens()
        ]);
    }
}