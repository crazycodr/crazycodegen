<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

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