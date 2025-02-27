<?php

namespace CrazyCodeGen\Renderers;

use CrazyCodeGen\Base\CanBeRendered;
use CrazyCodeGen\Traits\FlattenFunction;

class Renderer
{
    use FlattenFunction;

    public function render(CanBeRendered $target, RenderingRules $rules, RenderContext $context): string
    {
        foreach ($target->getTokens() as $token) {
            if ($token instanceof ContextShift) {
                $previousContextType = $context->getCurrent();
                $context->applyContextShift($token);
                $newContextType = $context->getCurrent();
                $rules->applyContextShiftToBuffer($previousContextType, $newContextType, $context);
            } else {
                $rules->applyTokenPrefix($token, $context);
                $rules->applyToken($token, $context);
                $rules->applyTokenSuffix($token, $context);
            }
        }
        return $context->buffer;
    }
}