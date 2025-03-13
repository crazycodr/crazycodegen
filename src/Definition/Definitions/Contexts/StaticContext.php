<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Base\DefinesIfStaticallyAccessed;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Operations\ChainToTrait;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StaticToken;

class StaticContext extends Expression implements DefinesIfStaticallyAccessed, MemberAccessContext
{
    use ChainToTrait;

    public function __construct()
    {
        parent::__construct(new StaticToken());
    }

    public function shouldAccessWithStatic(): bool
    {
        return true;
    }
}
