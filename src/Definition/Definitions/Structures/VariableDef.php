<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class VariableDef extends Tokenizes implements ProvidesCallableReference
{
    use FlattenFunction;

    public function __construct(
        public string|Token $name,
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
        $tokens[] = new DollarToken();
        if (is_string($this->name)) {
            $tokens[] = new Token($this->name);
        } else {
            $tokens[] = $this->name;
        }
        $tokens[] = new SpacesToken($context->argumentDefinitionIdentifierPaddingSize);
        return $this->flatten($tokens);
    }

    public function getCallableReference(): Expression
    {
        return new Expression($this);
    }
}
