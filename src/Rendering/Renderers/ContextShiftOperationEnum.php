<?php

namespace CrazyCodeGen\Rendering\Renderers;

enum ContextShiftOperationEnum: string
{
    case push = 'push';
    case pop = 'pop';
}