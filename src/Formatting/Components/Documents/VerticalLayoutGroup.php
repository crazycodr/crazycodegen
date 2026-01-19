<?php

namespace CrazyCodeGen\Formatting\Components\Documents;

use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\PrintableComponent;

class VerticalLayoutGroup implements DocumentComponent
{
    public function __construct(
        /** @var array<PrintableComponent> $subComponents */
        public array $subComponents = [],
    ) {
    }
}
