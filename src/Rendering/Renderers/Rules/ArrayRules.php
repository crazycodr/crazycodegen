<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;

class ArrayRules
{
    public function __construct(
        public bool              $useShortForm = true,
        public WrappingDecision  $wrap = WrappingDecision::IF_TOO_LONG,
        public BracePositionEnum $openingBrace = BracePositionEnum::DIFF_LINE,
        public BracePositionEnum $closingBrace = BracePositionEnum::DIFF_LINE,
        public bool              $padIdentifiers = false,
        public int               $spacesAfterIdentifiers = 1,
        public int               $spacesAfterOperators = 1,
        public int               $spacesAfterValues = 0,
        public int               $spacesAfterSeparators = 1,
        public bool              $addSeparatorToLastItem = true,
    ) {
    }
}
