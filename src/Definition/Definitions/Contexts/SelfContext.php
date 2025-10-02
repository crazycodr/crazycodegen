<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Operations\ChainToTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;

class SelfContext extends Tokenizes implements ShouldBeAccessedStatically, MemberAccessContext
{
    use ChainToTrait;

    public function getTokens(RenderingContext $context): array
    {
        return [new SelfToken()];
    }

    public function isAccessedStatically(): bool
    {
        return true;
    }
}
