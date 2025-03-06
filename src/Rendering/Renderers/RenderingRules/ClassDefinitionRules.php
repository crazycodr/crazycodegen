<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;

class ClassDefinitionRules
{
    public function __construct(
        public WrappingDecision  $extendsOnNextLine = WrappingDecision::IF_TOO_LONG,
        public WrappingDecision  $implementsOnNextLine = WrappingDecision::IF_TOO_LONG,
        public WrappingDecision  $implementsOnDifferentLines = WrappingDecision::IF_TOO_LONG,
        public int               $spacesAfterImplementsKeyword = 1,
        public int               $spacesAfterImplementCommaIfSameLine = 1,
        public BracePositionEnum $classOpeningBrace = BracePositionEnum::NEXT_LINE,
        public BracePositionEnum $classClosingBrace = BracePositionEnum::NEXT_LINE,
        public int               $spacesBeforeOpeningBraceIfSameLine = 1,
    )
    {

    }

    public function clone(): self
    {
        return clone $this;
    }
}