<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StaticToken;

class StaticRefTokenGroup extends ExpressionTokenGroup
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new StaticToken());
    }
}
