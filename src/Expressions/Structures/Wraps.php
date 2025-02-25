<?php

namespace CrazyCodeGen\Expressions\Structures;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

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