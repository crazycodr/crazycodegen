<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ExclamationToken;

class NotOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public int|float|string|bool|Tokenizes $operand,
        public bool                            $doubled = false,
    ) {
        if ($this->isInferableValue($this->operand)) {
            $this->operand = $this->inferValue($this->operand);
        }
    }

    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new ExclamationToken();
        $tokens[] = $this->operand->getTokens($context);
        return $this->flatten($tokens);
    }
}
