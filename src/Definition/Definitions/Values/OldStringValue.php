<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

/**
 * @deprecated
 */
class OldStringValue implements CanBeComputed
{
    public function __construct(
        public string $value,
    ) {
    }
}
