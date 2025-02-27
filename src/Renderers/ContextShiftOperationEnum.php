<?php

namespace CrazyCodeGen\Renderers;

enum ContextShiftOperationEnum: string
{
    case push = 'push';
    case pop = 'pop';
}