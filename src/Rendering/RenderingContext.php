<?php

namespace CrazyCodeGen\Rendering;

class RenderingContext
{
    public function __construct(
        public array $importedClasses = [],
    ) {
    }
}
