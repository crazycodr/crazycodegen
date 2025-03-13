<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Definition\Definitions\Structures\Types\ClassTypeDef;

interface ProvidesClassType
{
    public function getClassType(): ClassTypeDef;
}
