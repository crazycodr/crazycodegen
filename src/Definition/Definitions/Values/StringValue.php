<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\CanBeComputed;

class StringValue implements CanBeComputed
{
    public function __construct(
        public string $value,
    )
    {
    }

    public function getTokens(): array
    {
        return ["'", str_replace("'", "\\'", $this->value), "'"];
    }
}
