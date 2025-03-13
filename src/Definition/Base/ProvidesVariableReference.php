<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;

interface ProvidesVariableReference
{
    public function getVariableReference(): VariableDef;
}
