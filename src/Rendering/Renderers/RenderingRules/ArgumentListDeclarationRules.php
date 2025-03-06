<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

class ArgumentListDeclarationRules
{
    public function __construct(
        public int  $spacesAfterArgumentComma = 1,
        public bool $addTrailingCommaToLastItemInChopDown = true,
        public bool $padTypeNames = false,
        public bool $padIdentifiers = false,
    )
    {
    }

    public function clone(): self
    {
        return clone $this;
    }
}