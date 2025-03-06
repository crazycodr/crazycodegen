<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class PropertyRules
{
    public function __construct(
        public int $newLinesAfterDocBlock = 1,
        public int $spacesAfterVisibility = 1,
        public int $spacesAfterStatic = 1,
        public int $spacesAfterType = 1,
        public int $spacesAfterIdentifier = 1,
        public int $spacesAfterEquals = 1,
    ) {
    }
}
