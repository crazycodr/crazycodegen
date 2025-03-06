<?php

namespace CrazyCodeGen\Rendering\Tokens;

use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;

class TokenGroupPreRenderCache
{
    private null|int $length = null;

    public function __construct(
        /** @var Token[] $tokens */
        public readonly array $tokens = [],
        /** @var Token[] $additionalTokens */
        public readonly array $additionalTokens = [],
        public readonly RenderContext $context,
        public readonly RenderingRules $rules,
    )
    {
    }

    public function getLength(): int
    {
        if (!$this->length) {
            $buffer = '';
            foreach ($this->tokens as $token) {
                $buffer .= $token->render();
            }
            foreach ($this->additionalTokens as $token) {
                $buffer .= $token->render();
            }
            $this->length = strlen($buffer);
        }
        return $this->length;
    }
}