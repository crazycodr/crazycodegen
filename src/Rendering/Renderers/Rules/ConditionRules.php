<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;

class ConditionRules
{
    public function __construct(
        public int               $spacesBeforeParentheses = 1,
        public BracePositionEnum $startBrace = BracePositionEnum::SAME_LINE,
        public BracePositionEnum $elseAfterEndBrace = BracePositionEnum::NEXT_LINE,
        public int               $spacesBeforeBraceOnSameLine = 1,
        public int               $spacesAfterBraceOnSameLine = 1,
    )
    {
    }
}