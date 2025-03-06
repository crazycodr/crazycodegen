<?php

namespace CrazyCodeGen\Rendering\Renderers;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class Renderer
{
    use FlattenFunction;

    public function render(TokenGroup $tokenGroup, RenderContext $context, RenderingRules $rules): string
    {
        $buffer = '';
        $tokens = $tokenGroup->render($context, $rules);
        foreach ($tokens as $token) {
            $buffer .= $token->render();
        };
        return $buffer;
    }
}