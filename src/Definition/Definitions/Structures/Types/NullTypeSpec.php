<?php

namespace CrazyCodeGen\Definition\Definitions\Structures\Types;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class NullTypeSpec extends TypeDef
{
    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new NullToken()];
    }
}
