<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

interface ProvidesReference
{
    public function getReference(): Tokenizes;
}
