<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ThisToken;

class ThisRefTokenGroup extends VariableTokenGroup
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new ThisToken());
    }
}
