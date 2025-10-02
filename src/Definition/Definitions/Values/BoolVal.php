<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FalseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\TrueToken;

class BoolVal extends BaseVal
{
    public function __construct(
        public bool $value,
    ) {
    }

    public function getTokens(RenderingContext $context): array
    {
        return [$this->value ? new TrueToken() : new FalseToken()];
    }
}
