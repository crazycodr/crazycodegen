<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expressions\Operations\ChainToTrait;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ThisToken;

class ThisContext extends VariableDef implements MemberAccessContext
{
    use ChainToTrait;

    public function __construct()
    {
        parent::__construct(new ThisToken());
    }
}
