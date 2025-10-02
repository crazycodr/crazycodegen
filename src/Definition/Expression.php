<?php

namespace CrazyCodeGen\Definition;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\Token;

class Expression extends Tokenizes
{
    use FlattenFunction;

    public function __construct(
        public string $expression,
    ) {
    }

    /**
     * @param RenderingContext $context
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new Token($this->expression);
        return $this->flatten($tokens);
    }
}
