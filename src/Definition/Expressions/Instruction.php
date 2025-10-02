<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class Instruction extends Tokenizes
{
    use FlattenFunction;

    /**
     * @var Tokenizes[]
     */
    public readonly array $expressions;

    /**
     * @param string|Tokenizes|Tokenizes[] $expressions
     */
    public function __construct(
        string|array|Tokenizes $expressions,
    ) {
        if ($expressions instanceof Tokenizes) {
            $this->expressions = [$expressions];
        } elseif (is_string($expressions)) {
            $this->expressions = [new Expression($expressions)];
        } else {
            $this->expressions = $expressions;
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        foreach ($this->expressions as $expression) {
            $tokens[] = $expression->getTokens($context);
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
