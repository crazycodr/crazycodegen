<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class InstructionTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        /** @var int|float|bool|string|Token[]|Token|TokenGroup */
        public readonly int|float|bool|string|array|Token|TokenGroup $instructions,
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
        } elseif (is_array($this->instructions)) {
            foreach ($this->instructions as $token) {
                if ($token instanceof TokenGroup) {
                    $tokens[] = $token->render($context, $rules);
                } else {
                    $tokens[] = $token;
                }
            }
        } else {
            $tokens[] = $this->makeComputed($this->instructions)->getTokens();
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }

    public function getContexts(): array
    {
        return [ContextTypeEnum::instruction];
    }
}