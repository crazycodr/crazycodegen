<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class NamespaceRules
{
    public function __construct(
        public int $spacesAfterNamespace = 1,
        public int $newLinesAfterSemiColon = 2,
    ) {
    }
}
