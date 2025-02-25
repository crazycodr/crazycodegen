<?php

namespace CrazyCodeGen\Definitions\Values;

use CrazyCodeGen\Base\CanBeComputed;

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
