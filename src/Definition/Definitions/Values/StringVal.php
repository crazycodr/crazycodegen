<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SingleQuoteToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class StringVal extends BaseVal
{
    public function __construct(
        public string $value,
    ) {
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        return [
            new SingleQuoteToken(),
            new Token(str_replace("'", "\\'", $this->value)),
            new SingleQuoteToken()
        ];
    }
}
