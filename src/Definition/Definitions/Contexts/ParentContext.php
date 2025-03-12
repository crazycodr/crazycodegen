<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\DefinesIfStaticallyAccessed;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ParentToken;

class ParentContext extends Expression implements DefinesIfStaticallyAccessed, MemberAccessContext
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new ParentToken());
    }

    public function shouldAccessWithStatic(): bool
    {
        return true;
    }

    public static function to(PropertyDef|MethodDef|CallOp|MemberAccessContext $what): ChainOp
    {
        return new ChainOp([new self(), $what]);
    }
}
