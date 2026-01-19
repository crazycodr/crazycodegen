<?php

namespace CrazyCodeGen\Formatting\Components\Documents;

use CrazyCodeGen\Formatting\Components\DocumentComponent;

class Text implements DocumentComponent
{
    public function __construct(
        public string $text = '',
    ) {
    }
}