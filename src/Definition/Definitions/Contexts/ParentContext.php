<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Operations\ChainToTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ParentToken;

class ParentContext extends Tokenizes implements ShouldBeAccessedStatically, MemberAccessContext
{
    use ChainToTrait;

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new ParentToken()];
    }

    public function isAccessedStatically(): bool
    {
        return true;
    }
}
