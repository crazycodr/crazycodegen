<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

/**
 * @deprecated
 */
class NullValue implements CanBeComputed
{
    public function getTokens(): array
    {
        return ['null'];
    }
}
