<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class Wraps implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeComputed|int|float|string|bool $wrappedOperand,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            '(',
            $this->makeComputed($this->wrappedOperand)->getTokens(),
            ')'
        ]);
    }
}