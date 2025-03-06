<?php

namespace CrazyCodeGen\Rendering\Renderers;

enum RenderWrapDecisionEnum
{
    case INLINE;
    case WRAP;
    case CHOP_DOWN;
}