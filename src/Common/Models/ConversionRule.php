<?php

namespace CrazyCodeGen\Common\Models;

class ConversionRule
{
    public function __construct(
        public string      $inputType,
        public null|string $outputType = null,
        /** @var string[] $propertyPaths */
        public array       $propertyPaths = [],
        public mixed       $filter = null,
    ) {
        if (!is_callable($this->filter)) {
            $this->filter = null;
        }
    }
}
