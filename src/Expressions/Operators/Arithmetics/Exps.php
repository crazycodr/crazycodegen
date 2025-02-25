<?php

namespace CrazyCodeGen\Expressions\Operators\Arithmetics;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

class Exps implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeComputed|int|float|string|bool $left,
        public CanBeComputed|int|float|string|bool $right,
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            $this->makeComputed($this->left)->getTokens(),
            '**',
            $this->makeComputed($this->right)->getTokens()
        ]);
    }
}