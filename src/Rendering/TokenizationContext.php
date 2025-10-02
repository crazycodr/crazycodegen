<?php

namespace CrazyCodeGen\Rendering;

class TokenizationContext
{
    public function __construct(
        public array                  $importedClasses = [],
    ) {
    }
}
