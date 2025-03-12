<?php

namespace CrazyCodeGen\Definition\Definitions\Contexts;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ThisToken;

class ThisContext extends VariableDef implements MemberAccessContext
{
    use FlattenFunction;

    public function __construct()
    {
        parent::__construct(new ThisToken());
    }

    public static function to(PropertyDef|MethodDef|CallOp|MemberAccessContext $what): ChainOp
    {
        return new ChainOp([new self(), $what]);
    }
}
