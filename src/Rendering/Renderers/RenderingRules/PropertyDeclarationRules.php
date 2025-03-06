<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

class PropertyDeclarationRules
{
    public function __construct(
        public int $spacesAfterVisibility = 1,
        public int $spacesAfterStaticKeyword = 1,
        public int $spacesAfterType = 1,
        public int $spacesAfterIdentifier = 1,
        public int $spacesAfterEquals = 1,
    )
    {
    }
}