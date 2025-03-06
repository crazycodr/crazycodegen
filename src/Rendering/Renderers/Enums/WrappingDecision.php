<?php

namespace CrazyCodeGen\Rendering\Renderers\Enums;

enum WrappingDecision
{
    case NEVER;
    case ALWAYS;
    case IF_TOO_LONG;
}
