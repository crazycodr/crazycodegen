<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Definition\Expressions\Expression;

interface ProvidesCallableReference
{
    public function getCallableReference(): Expression;
}
