<?php

namespace CrazyCodeGen\Rendering\Renderers\Enums;

enum ChopWrapDecisionEnum
{
    case NEVER_WRAP;
    case ALWAYS_CHOP_OR_WRAP;
    case CHOP_OR_WRAP_IF_TOO_LONG;
}