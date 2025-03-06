<?php

namespace CrazyCodeGen\Rendering\Tokens;

use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;

interface CanRenderMultipleScenarios
{
    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return array<Token[]>
     */
    public function renderScenarios(RenderContext $context, RenderingRules $rules): array;
}