<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ReturnToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class ReturnOp extends Instruction
{
    use FlattenFunction;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(
        /** @var string|Token[]|Token|Tokenizes */
        public string|array|Token|Tokenizes $instructions,
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
        $tokens[] = new ReturnToken();
        $tokens[] = new SpacesToken();
        $tokens = array_merge($tokens, parent::getTokens($context, $rules));
        return $this->flatten($tokens);
    }
}
