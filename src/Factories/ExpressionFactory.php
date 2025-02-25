<?php

namespace CrazyCodeGen\Factories;

use CrazyCodeGen\Base\CanBeAssigned;
use CrazyCodeGen\Base\CanBeComputed;
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
    public function assigns(CanBeAssigned $left, CanBeComputed $right): Assigns
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

    public function equals(CanBeComputed $left, CanBeComputed $right, bool $softEquals = false): Equals
    {
        return new Equals(left: $left, right: $right, soft: $softEquals);
    }

    public function notEquals(CanBeComputed $left, CanBeComputed $right, bool $softEquals = false, bool $useLtGt = false): NotEquals
    {
        return new NotEquals(left: $left, right: $right, soft: $softEquals, useLtGt: $useLtGt);
    }

    public function ands(CanBeComputed $left, CanBeComputed $right, bool $textVersion = false): Ands
    {
        return new Ands(left: $left, right: $right, textBased: $textVersion);
    }

    public function ors(CanBeComputed $left, CanBeComputed $right, bool $textVersion = false): Ors
    {
        return new Ors(left: $left, right: $right, textBased: $textVersion);
    }

    public function xors(CanBeComputed $left, CanBeComputed $right): Xors
    {
        return new Xors(left: $left, right: $right);
    }

    public function nots(CanBeComputed $operand, bool $doubled = false): Nots
    {
        return new Nots(operand: $operand, doubled: $doubled);
    }

    public function concats(CanBeComputed $left, CanBeComputed $right): Concats
    {
        return new Concats(left: $left, right: $right);
    }

    public function concatAssigns(CanBeAssigned $left, CanBeComputed $right): ConcatAssigns
    {
        return new ConcatAssigns(left: $left, right: $right);
    }

    public function gt(CanBeComputed $left, CanBeComputed $right): IsGreaterThan
    {
        return new IsGreaterThan(left: $left, right: $right);
    }

    public function gte(CanBeComputed $left, CanBeComputed $right): IsGreaterThanOrEqualTo
    {
        return new IsGreaterThanOrEqualTo(left: $left, right: $right);
    }

    public function lt(CanBeComputed $left, CanBeComputed $right): IsLessThan
    {
        return new IsLessThan(left: $left, right: $right);
    }

    public function lte(CanBeComputed $left, CanBeComputed $right): IsLessThanOrEqualTo
    {
        return new IsLessThanOrEqualTo(left: $left, right: $right);
    }

    public function adds(CanBeComputed $left, CanBeComputed $right): Adds
    {
        return new Adds(left: $left, right: $right);
    }

    public function divs(CanBeComputed $left, CanBeComputed $right): Divs
    {
        return new Divs(left: $left, right: $right);
    }

    public function exps(CanBeComputed $left, CanBeComputed $right): Exps
    {
        return new Exps(left: $left, right: $right);
    }

    public function mods(CanBeComputed $left, CanBeComputed $right): Mods
    {
        return new Mods(left: $left, right: $right);
    }

    public function mults(CanBeComputed $left, CanBeComputed $right): Mults
    {
        return new Mults(left: $left, right: $right);
    }

    public function subs(CanBeComputed $left, CanBeComputed $right): Subs
    {
        return new Subs(left: $left, right: $right);
    }

    public function wraps(CanBeComputed $wrappedOperand): Wraps
    {
        return new Wraps(wrappedOperand: $wrappedOperand);
    }
}