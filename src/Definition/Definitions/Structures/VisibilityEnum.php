<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

enum VisibilityEnum: string
{
    case PUBLIC = 'public';
    case PROTECTED = 'protected';
    case PRIVATE = 'private';
}