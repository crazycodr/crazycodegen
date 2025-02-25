<?php

namespace CrazyCodeGen\Renderers;

use CrazyCodeGen\Base\CanBeRendered;

class Renderer
{
    public function render(CanBeRendered $target, RenderingRules $rules, RenderContext $context): string
    {
        $tokens = $target->getTokens();
        $target = '';
        foreach ($tokens as $token) {
            $target .= $rules->getPrefixForToken($token, $context);
            $target .= $token;
            $target .= $rules->getSuffixForToken($token, $context);
        }
        return $target;
    }
}