<?php

namespace CrazyCodeGen\Expressions\Operations;

use CrazyCodeGen\Base\CanBeCalled;
use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Traits\CallableTrait;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

class Calls implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;
    use CallableTrait;

    public function __construct(
        public CanBeCalled|string $target,
        public array  $arguments = [],
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