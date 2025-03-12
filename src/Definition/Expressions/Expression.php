<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

class Expression extends Tokenizes
{
    use FlattenFunction;

    public function __construct(
        /** @var string|Token[]|Token|Tokenizes */
        public string|array|Token|Tokenizes $instructions,
    )
    {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->instructions instanceof Tokenizes) {
            $tokens[] = $this->instructions->getTokens($context, $rules);
        } elseif ($this->instructions instanceof Token) {
            $tokens[] = $this->instructions;
        } elseif (is_string($this->instructions)) {
            $tokens[] = new Token($this->instructions);
        } else {
            foreach ($this->instructions as $instruction) {
                if ($instruction instanceof Tokenizes) {
                    $tokens[] = $instruction->getTokens($context, $rules);
                } else {
                    $tokens[] = $instruction;
                }
            }
        }
        return $this->flatten($tokens);
    }
}
