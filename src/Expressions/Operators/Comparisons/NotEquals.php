<?php

namespace CrazyCodeGen\Expressions\Operators\Comparisons;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

class NotEquals implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeComputed|int|float|string|bool $left,
        public CanBeComputed|int|float|string|bool $right,
        public bool                                $soft = false,
        public bool                                $useLtGt = false
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            $this->makeComputed($this->left)->getTokens(),
            $this->useLtGt ? '<>' : ($this->soft ? '!=' : '!=='),
            $this->makeComputed($this->right)->getTokens()
        ]);
    }
}