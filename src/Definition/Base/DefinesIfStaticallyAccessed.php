<?php

namespace CrazyCodeGen\Definition\Base;

interface DefinesIfStaticallyAccessed
{
    public function shouldAccessWithStatic(): bool;
}
