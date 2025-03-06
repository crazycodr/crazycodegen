<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Renderers\ContextShift;
use CrazyCodeGen\Definition\Renderers\ContextTypeEnum;
use CrazyCodeGen\Definition\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Definition\Traits\ComputableTrait;

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
        $tokens = array_merge($tokens, $this->makeComputed($this->wrappedInstruction)->getTokens());
        $tokens[] = new SemiColonToken();

        return $this->flatten($tokens);
    }
}