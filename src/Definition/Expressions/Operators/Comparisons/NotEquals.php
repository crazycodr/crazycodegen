<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Comparisons;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

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