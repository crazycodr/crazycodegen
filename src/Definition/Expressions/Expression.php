<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Defines;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;

class Expression extends Defines
{
    use FlattenFunction;

    public function __construct(
        /** @var Token[]|Token|Defines */
        public array|Token|Defines $instructions,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->instructions instanceof Defines) {
            $tokens[] = $this->instructions->getTokens($context, $rules);
        } elseif ($this->instructions instanceof Token) {
            $tokens[] = $this->instructions;
        } else {
            foreach ($this->instructions as $instruction) {
                if ($instruction instanceof Defines) {
                    $tokens[] = $instruction->getTokens($context, $rules);
                } else {
                    $tokens[] = $instruction;
                }
            }
        }
        return $this->flatten($tokens);
    }
}
