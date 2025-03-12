<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IssetToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class IssetOp extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|Token|Tokenizes $operand,
    ) {
        if (is_string($this->operand)) {
            $this->operand = new Token($this->operand);
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new IssetToken();
        $tokens[] = new ParStartToken();
        if ($this->operand instanceof Tokenizes) {
            $tokens[] = $this->operand->getTokens($context, $rules);
        } else {
            $tokens[] = $this->operand;
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
