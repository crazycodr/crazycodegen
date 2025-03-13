<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

class FloatVal extends BaseVal
{
    public function __construct(
        public float $value,
    ) {
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new Token($this->value)];
    }
}
