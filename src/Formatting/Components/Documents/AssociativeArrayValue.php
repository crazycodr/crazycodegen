<?php

namespace CrazyCodeGen\Formatting\Components\Documents;

use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\PrintableComponent;

class AssociativeArrayValue implements DocumentComponent
{
    public function __construct(
        /** @var PrintableComponent[] $subComponents */
        public array $subComponents = [],
    ) {
    }
}
