<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Comparisons;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\InstanceOfToken;

class InstanceOfOp extends Tokenizes
{
    use FlattenFunction;
    use ComputableTrait;

    public function __construct(
        public Tokenizes|int|float|string|bool $left,
        public Tokenizes|int|float|string|bool $right,
    ) {
        $this->left = $this->getValOrReturn($this->left);
        $this->right = $this->getValOrReturn($this->right);
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = $this->left->getTokens($context, $rules);
        $tokens[] = new SpacesToken();
        $tokens[] = new InstanceOfToken();
        $tokens[] = new SpacesToken();
        $tokens[] = $this->right->getTokens($context, $rules);
        return $this->flatten($tokens);
    }
}
