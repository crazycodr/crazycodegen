<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;

trait ChainToTrait
{
    public static function to(PropertyDef|CallOp $what): ChainOp
    {
        return new ChainOp([new self(), $what]);
    }
}
