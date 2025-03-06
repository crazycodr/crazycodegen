<?php

namespace CrazyCodeGen\Rendering\Tokens;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;

abstract class TokenGroup
{
    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    abstract public function render(RenderContext $context, RenderingRules $rules): array;

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return [];
    }
}