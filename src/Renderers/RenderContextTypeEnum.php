<?php

namespace CrazyCodeGen\Renderers;

use CrazyCodeGen\Base\CanBeRendered;

enum RenderContextTypeEnum
{
    case none;
    case classDef;
}