<?php

namespace CrazyCodeGen\Formatting\Components\Documents;

use CrazyCodeGen\Formatting\Components\DocumentComponent;

class AssociativeArrayGroup implements DocumentComponent
{
    public function __construct(
        /** @var AssociativeArrayPair[] $pairs */
        public array $pairs = [],
    ) {
    }
}
