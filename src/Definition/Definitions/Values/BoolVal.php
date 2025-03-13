<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FalseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\TrueToken;

class BoolVal extends BaseVal
{
    public function __construct(
        public bool $value,
    ) {
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [$this->value ? new TrueToken() : new FalseToken()];
    }
}
