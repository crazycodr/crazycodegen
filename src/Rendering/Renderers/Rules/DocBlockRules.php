<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class DocBlockRules
{
    public function __construct(
        public int $lineLength = 80,
    ) {
    }
}
