<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SingleQuoteToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class StringVal extends BaseVal
{
    public function __construct(
        public string $value,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [
            new SingleQuoteToken(),
            new Token(str_replace("'", "\\'", $this->value)),
            new SingleQuoteToken()
        ];
    }
}
