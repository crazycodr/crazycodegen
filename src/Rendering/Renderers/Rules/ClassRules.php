<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;

class ClassRules
{
    public function __construct(
        public int               $newLinesAfterDocBlock = 1,
        public WrappingDecision  $extendsOnNextLine = WrappingDecision::IF_TOO_LONG,
        public WrappingDecision  $implementsOnNextLine = WrappingDecision::IF_TOO_LONG,
        public WrappingDecision  $implementsOnDifferentLines = WrappingDecision::IF_TOO_LONG,
        public int               $spacesAfterImplements = 1,
        public int               $spacesAfterImplementSeparator = 1,
        public BracePositionEnum $openingBrace = BracePositionEnum::NEXT_LINE,
        public BracePositionEnum $closingBrace = BracePositionEnum::NEXT_LINE,
        public int               $spacesBeforeOpeningBrace = 1,
        public int               $newLinesAfterEachImport = 1,
        public int               $newLinesAfterAllImports = 2,
        public int               $newLinesAfterEachProperty = 1,
        public int               $newLinesAfterProperties = 2,
        public int               $newLinesAfterEachMethod = 2,
        public int               $newLinesAfterClosingBrace = 1,
    )
    {
    }
}