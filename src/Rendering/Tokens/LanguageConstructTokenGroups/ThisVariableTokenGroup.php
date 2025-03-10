<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;

class ThisVariableTokenGroup extends VariableTokenGroup
{
    use FlattenFunction;

    public function __construct() {
        parent::__construct('this');
    }
}
