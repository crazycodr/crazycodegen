<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class ImportRules
{
    public function __construct(
        public int $spacesAfterUse = 1,
        public int $spacesAfterType = 1,
        public int $spacesAfterAs = 1,
    )
    {
    }
}