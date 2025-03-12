<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ExclamationToken;

class NotOp extends Tokenizes
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public int|float|string|bool|Tokenizes $operand,
        public bool                            $doubled = false,
    )
    {
        $this->operand = $this->getValOrReturn($this->operand);
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new ExclamationToken();
        $tokens[] = $this->operand->getTokens($context, $rules);
        return $this->flatten($tokens);
    }
}
