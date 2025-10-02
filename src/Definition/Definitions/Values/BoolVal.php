<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FalseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\TrueToken;

class BoolVal extends BaseVal
{
    public function __construct(
        public bool $value,
    ) {
    }

    public function getSimpleTokens(TokenizationContext $context): array
    {
        return [$this->value ? new TrueToken() : new FalseToken()];
    }
}
