<?php

namespace CrazyCodeGen\Rendering\Renderers;

use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class Renderer
{
    use FlattenFunction;
    use ComputableTrait;

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