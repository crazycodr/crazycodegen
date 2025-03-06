<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class ArgumentListRules
{
    public function __construct(
        public int  $spacesAfterSeparator = 1, /** @todo unused? */
        public bool $addSeparatorToLastItem = true,
        public bool $padTypes = false,
        public bool $padIdentifiers = false,
    )
    {
    }
}