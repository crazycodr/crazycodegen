<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Base\DefinesIfStaticallyAccessed;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Operations\ChainToTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;

class SelfContext extends Tokenizes implements DefinesIfStaticallyAccessed, MemberAccessContext
{
    use ChainToTrait;

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return [new SelfToken()];
    }

    public function shouldAccessWithStatic(): bool
    {
        return true;
    }
}
