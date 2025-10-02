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

    public readonly Tokenizes $left;
    public readonly Tokenizes $right;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        Tokenizes|int|float|string|bool $left,
        Tokenizes|int|float|string|bool $right,
    ) {
        if ($left instanceof Tokenizes) {
            $this->left = $left;
        } else {
            $this->left = $this->inferValue($left);
        }
        if ($right instanceof Tokenizes) {
            $this->right = $right;
        } else {
            $this->right = $this->inferValue($right);
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
