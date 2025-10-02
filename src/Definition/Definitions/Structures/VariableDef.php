<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class VariableDef extends Tokenizes implements ProvidesCallableReference
{
    use FlattenFunction;

    public function __construct(
        public string $name,
    ) {
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new DollarToken();
        if (is_string($this->name)) {
            $tokens[] = new Token($this->name);
        } else {
            $tokens[] = $this->name;
        }
        return $this->flatten($tokens);
    }

    public function getCallableReference(): Tokenizes
    {
        return $this;
    }
}
