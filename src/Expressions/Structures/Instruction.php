<?php

namespace CrazyCodeGen\Expressions\Structures;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Renderers\ContextShift;
use CrazyCodeGen\Renderers\ContextTypeEnum;
use CrazyCodeGen\Traits\ComputableTrait;
use CrazyCodeGen\Traits\FlattenFunction;

class Instruction implements CanBeComputed
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public CanBeComputed|int|float|string|bool $wrappedInstruction,
    )
    {
    }

    public function getTokens(): array
    {
        $tokens = [];
        $tokens[] = ContextShift::push(ContextTypeEnum::instruction);
        $tokens = array_merge($tokens, $this->makeComputed($this->wrappedInstruction)->getTokens());
        $tokens[] = ';';
        $tokens[] = ContextShift::pop(ContextTypeEnum::instruction);

        return $this->flatten($tokens);
    }
}