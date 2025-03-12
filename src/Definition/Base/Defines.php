<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

abstract class Defines
{
    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    abstract public function getTokens(RenderContext $context, RenderingRules $rules): array;
}
