<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;

interface ProvidesClassReference
{
    public function getClassReference(): ClassRefVal;
}
