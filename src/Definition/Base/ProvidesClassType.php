<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

interface ProvidesClassType
{
    public function getClassType(): ClassTypeDef;
}
