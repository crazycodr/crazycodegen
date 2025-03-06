<?php

namespace CrazyCodeGen\Rendering\Tokens;

class Token
{
    public function __construct(public readonly string $text)
    {
    }

    public function render(): string
    {
        return $this->text;
    }
}
