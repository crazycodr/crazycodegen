<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

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