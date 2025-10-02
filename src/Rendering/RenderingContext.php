<?php

namespace CrazyCodeGen\Rendering;

class RenderingContext
{
    public function __construct(
        /** @var array<string> $importedClasses */
        public array $importedClasses = [],
    ) {
    }
}
