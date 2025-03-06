<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;

class FunctionDefinitionRules
{
    public function __construct(
        public WrappingDecision  $argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG,
        public BracePositionEnum $funcOpeningBrace = BracePositionEnum::NEXT_LINE,
        public BracePositionEnum $funcClosingBrace = BracePositionEnum::NEXT_LINE,
        public int               $spacesBetweenIdentifierAndArgumentList = 0,
        public int               $spacesBetweenArgumentListAndReturnColon = 0,
        public int               $spacesBetweenReturnColonAndType = 1,
        public int               $spacesBeforeOpeningBraceIfSameLine = 1,
    )
    {

    }

    public function clone(): self
    {
        return clone $this;
    }
}