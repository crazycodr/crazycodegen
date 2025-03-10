<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;

class SelfRefTokenGroup extends ExpressionTokenGroup
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new SelfToken());
    }
}
