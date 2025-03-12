<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;

class NullVal extends Tokenizes
{
    public function __construct()
    {
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new NullToken()];
    }
}
