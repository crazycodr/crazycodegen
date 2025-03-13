<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

class IntVal extends BaseVal
{
    public function __construct(
        public int $value,
    ) {
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new Token($this->value)];
    }
}
