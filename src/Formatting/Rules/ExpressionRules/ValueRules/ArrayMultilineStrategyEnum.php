<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules;

enum ArrayMultilineStrategyEnum: string
{
    case Preserve = 'preserve';
    case ChopDownIfLong = 'chop_down_if_long';
    case AlwaysMultiline = 'always_multiline';
}
