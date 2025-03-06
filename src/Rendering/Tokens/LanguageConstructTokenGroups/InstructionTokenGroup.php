<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class InstructionTokenGroup extends TokenGroup
{
    use FlattenFunction;

    public function __construct(
        /** @var Token[]|Token|TokenGroup */
        public readonly array|Token|TokenGroup $instructions,
    )
    {
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
            $tokens = array_merge($tokens, $this->instructions);
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }

    public function getContexts(): array
    {
        return [ContextTypeEnum::instruction];
    }
}