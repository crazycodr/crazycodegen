<?php

namespace CrazyCodeGen\Factories;

use CrazyCodeGen\Base\CanBeAssigned;
use CrazyCodeGen\Base\CanBeCalled;
use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Expressions\Operations\Calls;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Divs;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Exps;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Mods;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Mults;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Subs;
use CrazyCodeGen\Expressions\Operators\Assigns\Assigns;
use CrazyCodeGen\Expressions\Operators\Assigns\Decrements;
use CrazyCodeGen\Expressions\Operators\Assigns\Increments;
use CrazyCodeGen\Expressions\Operators\Comparisons\Equals;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsGreaterThan;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsGreaterThanOrEqualTo;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsLessThan;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsLessThanOrEqualTo;
use CrazyCodeGen\Expressions\Operators\Comparisons\NotEquals;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Ands;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Nots;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Ors;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Xors;
use CrazyCodeGen\Expressions\Operators\Strings\ConcatAssigns;
use CrazyCodeGen\Expressions\Operators\Strings\Concats;
use CrazyCodeGen\Expressions\Structures\Wraps;

class ExpressionFactory
{
    public function calls(CanBeCalled|string $target, array $arguments = []): Calls
    {
        return new Calls(target: $target, arguments: $arguments);
    }

    public function assigns(CanBeAssigned $left, CanBeComputed|int|float|string|bool $right): Assigns
    {
        return new Assigns(left: $left, right: $right);
    }

    public function decrements(CanBeAssigned $operand, bool $preDecrement = false): Decrements
    {
        return new Decrements(operand: $operand, pre: $preDecrement);
    }

    public function increments(CanBeAssigned $operand, bool $preIncrement = false): Increments
    {
        return new Increments(operand: $operand, pre: $preIncrement);
    }

    public function equals(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right, bool $softEquals = false): Equals
    {
        return new Equals(left: $left, right: $right, soft: $softEquals);
    }

    public function notEquals(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right, bool $softEquals = false, bool $useLtGt = false): NotEquals
    {
        return new NotEquals(left: $left, right: $right, soft: $softEquals, useLtGt: $useLtGt);
    }

    public function ands(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right, bool $textVersion = false): Ands
    {
        return new Ands(left: $left, right: $right, textBased: $textVersion);
    }

    public function ors(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right, bool $textVersion = false): Ors
    {
        return new Ors(left: $left, right: $right, textBased: $textVersion);
    }

    public function xors(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Xors
    {
        return new Xors(left: $left, right: $right);
    }

    public function nots(CanBeComputed|int|float|string|bool $operand, bool $doubled = false): Nots
    {
        return new Nots(operand: $operand, doubled: $doubled);
    }

    public function concats(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Concats
    {
        return new Concats(left: $left, right: $right);
    }

    public function concatAssigns(CanBeAssigned $left, CanBeComputed|int|float|string|bool $right): ConcatAssigns
    {
        return new ConcatAssigns(left: $left, right: $right);
    }

    public function gt(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): IsGreaterThan
    {
        return new IsGreaterThan(left: $left, right: $right);
    }

    public function gte(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): IsGreaterThanOrEqualTo
    {
        return new IsGreaterThanOrEqualTo(left: $left, right: $right);
    }

    public function lt(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): IsLessThan
    {
        return new IsLessThan(left: $left, right: $right);
    }

    public function lte(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): IsLessThanOrEqualTo
    {
        return new IsLessThanOrEqualTo(left: $left, right: $right);
    }

    public function adds(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Adds
    {
        return new Adds(left: $left, right: $right);
    }

    public function divs(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Divs
    {
        return new Divs(left: $left, right: $right);
    }

    public function exps(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Exps
    {
        return new Exps(left: $left, right: $right);
    }

    public function mods(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Mods
    {
        return new Mods(left: $left, right: $right);
    }

    public function mults(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Mults
    {
        return new Mults(left: $left, right: $right);
    }

    public function subs(CanBeComputed|int|float|string|bool $left, CanBeComputed|int|float|string|bool $right): Subs
    {
        return new Subs(left: $left, right: $right);
    }

    public function wraps(CanBeComputed|int|float|string|bool $wrappedOperand): Wraps
    {
        return new Wraps(wrappedOperand: $wrappedOperand);
    }
}