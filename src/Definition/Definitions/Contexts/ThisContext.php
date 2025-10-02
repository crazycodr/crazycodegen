<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Operations\ChainToTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ThisToken;

class ThisContext extends Tokenizes implements MemberAccessContext
{
    use ChainToTrait;

    public function getTokens(RenderingContext $context): array
    {
        return [new DollarToken(), new ThisToken()];
    }
}
