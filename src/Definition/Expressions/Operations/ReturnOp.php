<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ReturnToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class ReturnOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public mixed $instruction,
    ) {
        if ($this->isInferableValue($instruction)) {
            $this->instruction = $this->inferValue($this->instruction);
        } elseif (!$this->instruction instanceof Tokenizes) {
            $this->instruction = new Expression($this->instruction);
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new ReturnToken();
        $tokens[] = new SpacesToken();
        $tokens[] = $this->instruction->getTokens($context);
        return $this->flatten($tokens);
    }
}
