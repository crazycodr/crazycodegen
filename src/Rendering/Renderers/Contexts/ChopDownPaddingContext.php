<?php

namespace CrazyCodeGen\Rendering\Renderers\Contexts;

class ChopDownPaddingContext
{
    public function __construct(
        public null|int $paddingSpacesForVisibilities = null,
        public null|int $paddingSpacesForModifiers = null,
        public null|int $paddingSpacesForTypes = null,
        public null|int $paddingSpacesForIdentifiers = null,
    )
    {
    }
}