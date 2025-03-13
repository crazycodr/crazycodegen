<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ReturnToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class ReturnOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    public function __construct(
        public int|float|string|bool|array|Token|Tokenizes $instruction,
    ) {
        if ($this->isSupportedValue($instruction)) {
            $this->instruction = $this->inferValue($this->instruction);
        }
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
        $tokens[] = $this->instruction->getTokens($context, $rules);
        return $this->flatten($tokens);
    }
}
