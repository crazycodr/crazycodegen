<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Traits\CallableTrait;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

class Calls implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;
    use CallableTrait;

    public function __construct(
        public CanBeCalled|string $target,
        public array              $arguments = [],
    )
    {
    }

    public function getTokens(): array
    {
        return $this->flatten([
            $this->getCallReference($this->target),
            '(',
            $this->generateArgumentTokens(),
            ')'
        ]);
    }

    private function generateArgumentTokens(): array
    {
        $tokens = [];
        foreach ($this->arguments as $argument) {
            $tokens[] = $this->flatten($this->makeComputed($argument)->getTokens());
            $tokens[] = ',';
        }
        // Remove last comma
        array_pop($tokens);
        return $tokens;
    }
}