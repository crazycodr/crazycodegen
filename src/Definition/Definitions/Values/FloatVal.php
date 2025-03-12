<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

class FloatVal extends Tokenizes
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
