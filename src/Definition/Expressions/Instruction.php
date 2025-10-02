<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
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
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        foreach ($this->expressions as $expression) {
            $tokens[] = $expression->getSimpleTokens($context);
        }
        $tokens = $this->flatten($tokens);
        $lastToken = $tokens[array_key_last($tokens)];
        if ($lastToken instanceof BraceEndToken || $lastToken instanceof NewLinesToken) {
            return $tokens;
        }
        $tokens[] = new SemiColonToken();
        return $tokens;
    }
}
