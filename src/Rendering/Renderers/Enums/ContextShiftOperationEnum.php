<?php

namespace CrazyCodeGen\Rendering\Renderers\Enums;

enum ContextShiftOperationEnum: string
{
    case push = 'push';
    case pop = 'pop';
}