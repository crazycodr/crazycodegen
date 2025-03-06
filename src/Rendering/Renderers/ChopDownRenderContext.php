<?php

namespace CrazyCodeGen\Rendering\Renderers;

class ChopDownRenderContext
{
    public function __construct(
        public null|int $paddingSpacesForTypes = null,
        public null|int $paddingSpacesForIdentifiers = null,
    )
    {
    }
}