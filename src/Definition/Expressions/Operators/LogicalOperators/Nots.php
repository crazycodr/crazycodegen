<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

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