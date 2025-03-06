<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class ArgumentRules
{
    public function __construct(
        public int $spacesAfterType = 1,
        public int $spacesAfterIdentifier = 1,
        public int $spacesAfterEquals = 1,
    ) {
    }
}
