<?php

namespace CrazyCodeGen\Renderers;

use CrazyCodeGen\Base\CanBeComputed;

class RenderContext
{
    public function __construct(
        public string             $indents = '',
        public null|CanBeComputed $context = null,
    )
    {
    }
}