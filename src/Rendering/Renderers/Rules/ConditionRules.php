<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;

class ConditionRules
{
    public function __construct(
        public int               $spacesAfterKeyword = 1,
        public BracePositionEnum $openingBrace = BracePositionEnum::SAME_LINE,
        public BracePositionEnum $keywordAfterClosingBrace = BracePositionEnum::DIFF_LINE,
        public int               $spacesBeforeOpeningBrace = 1,
        public int               $spacesAfterClosingBrace = 1,
    ) {
    }
}
