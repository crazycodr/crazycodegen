<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\DefinesIfStaticallyAccessed;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;

class SelfContext extends Expression implements DefinesIfStaticallyAccessed
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new SelfToken());
    }

    public function shouldAccessWithStatic(): bool
    {
        return true;
    }
}
