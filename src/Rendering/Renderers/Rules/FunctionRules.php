<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;

class FunctionRules
{
    public function __construct(
        public int               $newLinesAfterDocBlock = 1,
        public WrappingDecision  $argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG,
        public BracePositionEnum $openingBrace = BracePositionEnum::NEXT_LINE,
        public BracePositionEnum $closingBrace = BracePositionEnum::NEXT_LINE,
        public int               $spacesAfterIdentifier = 0,
        public int               $spacesAfterArguments = 0,
        public int               $spacesAfterReturnColon = 1,
        public int               $spacesBeforeOpeningBrace = 1,
    )
    {
    }
}