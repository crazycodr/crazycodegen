<?php

namespace CrazyCodeGen\Formatting\Components\Documents;

use CrazyCodeGen\Formatting\Components\DocumentComponent;

class MultilineText implements DocumentComponent
{
    public function __construct(
        public string $text = '',
    ) {
    }
}