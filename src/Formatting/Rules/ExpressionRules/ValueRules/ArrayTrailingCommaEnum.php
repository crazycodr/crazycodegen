<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules;

enum ArrayTrailingCommaEnum: string
{
    case Never = 'never';
    case Always = 'always';
    case MultilineOnly = 'multiline_only';
}
