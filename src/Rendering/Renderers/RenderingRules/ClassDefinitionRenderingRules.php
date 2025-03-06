<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ChopWrapDecisionEnum;

class ClassDefinitionRenderingRules
{
    public function __construct(
        public ChopWrapDecisionEnum $extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG,
        public ChopWrapDecisionEnum $implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG,
        public ChopWrapDecisionEnum $implementsOnDifferentLines = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG,
        public int                  $spacesAfterImplementsKeyword = 1,
        public int                  $spacesAfterImplementCommaIfSameLine = 1,
        public BracePositionEnum    $classOpeningBrace = BracePositionEnum::NEXT_LINE,
        public BracePositionEnum    $classClosingBrace = BracePositionEnum::NEXT_LINE,
        public int                  $spacesBeforeOpeningBraceIfSameLine = 1,
    )
    {

    }

    public function clone(): self
    {
        return clone $this;
    }
}