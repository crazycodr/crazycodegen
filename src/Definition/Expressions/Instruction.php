<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class Instruction extends Tokenizes
{
    use FlattenFunction;

    public function __construct(
        /** @var string|Tokenizes[] */
        public string|array|Tokenizes $expressions,
    ) {
        if ($this->expressions instanceof Tokenizes) {
            $this->expressions = [$this->expressions];
        } elseif (is_string($this->expressions)) {
            $this->expressions = [new Expression($this->expressions)];
        }
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        foreach ($this->expressions as $expression) {
            $tokens[] = $expression->getTokens($context, $rules);
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
