<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class ExpressionTokenGroup extends TokenGroup
{
    use FlattenFunction;

    public function __construct(
        /** @var Token[]|Token|TokenGroup */
        public array|Token|TokenGroup $instructions,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->instructions instanceof TokenGroup) {
            $tokens[] = $this->instructions->render($context, $rules);
        } elseif ($this->instructions instanceof Token) {
            $tokens[] = $this->instructions;
        } else {
            foreach ($this->instructions as $instruction) {
                if ($instruction instanceof TokenGroup) {
                    $tokens[] = $instruction->render($context, $rules);
                } else {
                    $tokens[] = $instruction;
                }
            }
        }
        return $this->flatten($tokens);
    }
}
