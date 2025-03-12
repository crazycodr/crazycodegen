<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FalseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\TrueToken;

class BoolVal extends Tokenizes
{
    public function __construct(
        public bool $value,
    ) {
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        if ($this->value) {
            return [new TrueToken()];
        }
        return [new FalseToken()];
    }
}
