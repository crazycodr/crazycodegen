<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\IsStaticAccessContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;

class SelfRefTokenGroup extends ExpressionTokenGroup implements IsStaticAccessContext
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new SelfToken());
    }

    public function isStaticAccessContext(): bool
    {
        return true;
    }
}
