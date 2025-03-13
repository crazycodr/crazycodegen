<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;

class NullVal extends BaseVal
{
    public function __construct()
    {
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new NullToken()];
    }
}
