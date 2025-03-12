<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ThisToken;

class ThisContext extends VariableDef
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new ThisToken());
    }
}
