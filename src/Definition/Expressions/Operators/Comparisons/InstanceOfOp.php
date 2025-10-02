<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Comparisons;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\InstanceOfToken;

class InstanceOfOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public Tokenizes|int|float|string|bool $left,
        public Tokenizes|int|float|string|bool $right,
    ) {
        if ($this->isInferableValue($this->left)) {
            $this->left = $this->inferValue($this->left);
        }
        if ($this->isInferableValue($this->right)) {
            $this->right = $this->inferValue($this->right);
        }
    }

    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = $this->left->getTokens($context);
        $tokens[] = new SpacesToken();
        $tokens[] = new InstanceOfToken();
        $tokens[] = new SpacesToken();
        $tokens[] = $this->right->getTokens($context);
        return $this->flatten($tokens);
    }
}
