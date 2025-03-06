<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

class DocBlockRules
{
    public function __construct(
        public int $lineLength = 80,
    )
    {
    }
}