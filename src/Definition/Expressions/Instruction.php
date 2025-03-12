<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Defines;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class Instruction extends Expression
{
    use FlattenFunction;

    /** @noinspection PhpMissingParentConstructorInspection */
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
        $tokens = array_merge($tokens, parent::getTokens($context, $rules));
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
