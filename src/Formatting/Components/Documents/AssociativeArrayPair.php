<?php

namespace CrazyCodeGen\Formatting\Components\Documents;

use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\PrintableComponent;

class AssociativeArrayPair implements DocumentComponent
{
    public function __construct(
        public ?AssociativeArrayKey $key = null,
        public ?PrintableComponent $arrow = null,
        public AssociativeArrayValue $value,
    ) {
    }

    public function hasKeyAndArrow(): bool
    {
        return $this->key !== null && $this->arrow !== null;
    }
}
