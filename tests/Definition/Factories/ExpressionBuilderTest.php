<?php

namespace CrazyCodeGen\Tests\Definition\Factories;

use CrazyCodeGen\Definition\Definitions\Values\BoolValue;
use CrazyCodeGen\Definition\Definitions\Values\FloatValue;
use CrazyCodeGen\Definition\Definitions\Values\IntValue;
use CrazyCodeGen\Definition\Definitions\Values\StringValue;
use CrazyCodeGen\Definition\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Divs;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Exps;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Mods;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Mults;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Subs;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\Equals;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsGreaterThan;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsGreaterThanOrEqualTo;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsLessThan;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsLessThanOrEqualTo;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\NotEquals;
use CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators\Ands;
use CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators\Nots;
use CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators\Ors;
use CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators\Xors;
use CrazyCodeGen\Definition\Expressions\Operators\Strings\Concats;
use CrazyCodeGen\Definition\Expressions\Structures\Wraps;
use CrazyCodeGen\Definition\Factories\ExpressionBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExpressionBuilderTest extends TestCase
{

    public static function providesPrecedence(): array
    {
        return [
            'simple-wraps' => ['input' => ['(', 1, ')'], 'output' => new Wraps(new IntValue(1))],
            'nested-wraps' => ['input' => ['(', '(', 1, ')', ')'], 'output' => new Wraps(new Wraps(new IntValue(1)))],
            'sided-wraps' => ['input' => ['(', 1, ')', '+', '(', 2, ')'], 'output' => new Adds(new Wraps(new IntValue(1)), new Wraps(new IntValue(2)))],
            'nested-sided-wraps' => ['input' => ['(', 1, ')', '+', '(', 2, '-', '(', 3, '*', 4, ')', ')'], 'output' => new Adds(new Wraps(new IntValue(1)), new Wraps(new Subs(2, new Wraps(new Mults(3, 4)))))],
            'exp-over-add' => ['input' => [1, '**', 2, '+', 3], 'output' => new Adds(new Exps(1, 2), 3)],
            'exp-exp-assoc' => ['input' => [1, '**', 2, '**', 3], 'output' => new Exps(1, new Exps(2, 3))],
            'not-over-and' => ['input' => ['!', true, '&&', false], 'output' => new Ands(new Nots(true), false)],
            'double-not-over-and' => ['input' => ['!', '!', true, '&&', true], 'output' => new Ands(new Nots(true, doubled: true), true)],
            'mult-over-add' => ['input' => [1, '*', 2, '+', 3], 'output' => new Adds(new Mults(1, 2), 3)],
            'div-over-add' => ['input' => [1, '/', 2, '+', 3], 'output' => new Adds(new Divs(1, 2), 3)],
            'mod-over-add' => ['input' => [1, '%', 2, '+', 3], 'output' => new Adds(new Mods(1, 2), 3)],
            'mod-div-mult-assoc' => ['input' => [1, '%', 2, '/', 3, '*', 4, '/', 5, '%', 6], 'output' => new Mods(new Divs(new Mults(new Divs(new Mods(1, 2), 3), 4), 5), 6)],
            'add-over-concat' => ['input' => [1, '+', 2, '.', 3], 'output' => new Concats(new Adds(1, 2), 3)],
            'sub-over-concat' => ['input' => [1, '-', 2, '.', 3], 'output' => new Concats(new Subs(1, 2), 3)],
            'add-sub-assoc' => ['input' => [1, '-', 2, '+', 3, '-', 4], 'output' => new Subs(new Adds(new Subs(1, 2), 3), 4)],
            'concat-over-lt' => ['input' => ['hello', '.', 'world', '<', 3], 'output' => new IsLessThan(new Concats('hello', 'world'), 3)],
            'lt-over-soft-equals' => ['input' => [1, '<', 2, '==', 3], 'output' => new Equals(new IsLessThan(1, 2), 3, soft: true)],
            'lte-over-soft-equals' => ['input' => [1, '<=', 2, '==', 3], 'output' => new Equals(new IsLessThanOrEqualTo(1, 2), 3, soft: true)],
            'gt-over-soft-equals' => ['input' => [1, '>', 2, '==', 3], 'output' => new Equals(new IsGreaterThan(1, 2), 3, soft: true)],
            'gte-over-soft-equals' => ['input' => [1, '>=', 2, '==', 3], 'output' => new Equals(new IsGreaterThanOrEqualTo(1, 2), 3, soft: true)],
            // lt, lte, gt and gte are non-assoc so not testing associativity
            'soft-equals-over-&&' => ['input' => [1, '==', 2, '&&', 3], 'output' => new Ands(new Equals(1, 2, soft: true), 3)],
            'soft-not-equals-over-&&' => ['input' => [1, '!=', 2, '&&', 3], 'output' => new Ands(new NotEquals(1, 2, soft: true), 3)],
            'equals-over-&&' => ['input' => [1, '===', 2, '&&', 3], 'output' => new Ands(new Equals(1, 2), 3)],
            'not-equals-over-&&' => ['input' => [1, '!==', 2, '&&', 3], 'output' => new Ands(new NotEquals(1, 2), 3)],
            'not-equals-text-over-&&' => ['input' => [1, '<>', 2, '&&', 3], 'output' => new Ands(new NotEquals(1, 2, useLtGt: true), 3)],
            // ==, !=, ===, !== and <> are non-assoc so not testing associativity
            '&&-over-||' => ['input' => [true, '&&', false, '||', true], 'output' => new Ors(new Ands(true, false), true)],
            '||-over-and' => ['input' => [true, 'and', false, '||', true], 'output' => new Ands(true, new Ors(false, true), textBased: true)],
            'and-over-xor' => ['input' => [true, 'xor', false, 'and', true], 'output' => new Xors(true, new Ands(false, true, textBased: true))],
            'xor-over-or' => ['input' => [true, 'or', false, 'xor', true], 'output' => new Ors(true, new Xors(false, true), textBased: true)],
        ];
    }

    #[DataProvider(methodName: 'providesPrecedence')]
    public function testPrecedence(array $input, $output): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(...$input);
        $this->assertEquals($output, $result);
    }

    public function testIntResultsInIntValue(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1);
        $this->assertEquals(new IntValue(1), $result);
    }

    public function testFloatResultsInFloatValue(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(3.14159265);
        $this->assertEquals(new FloatValue(3.14159265), $result);
    }

    public function testStringResultsInStringValue(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build('hello world');
        $this->assertEquals(new StringValue('hello world'), $result);
    }

    public function testBoolResultsInBoolValue(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(true);
        $this->assertEquals(new BoolValue(true), $result);

        $result = $builder->build(false);
        $this->assertEquals(new BoolValue(false), $result);
    }

    public function testArithmeticAdditionShouldBeUnderstoodAndParsedIntoProperComponent(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '+', 2);
        $this->assertEquals(new Adds(1, 2), $result);
    }

    public function testArithmeticSubtractionShouldBeUnderstoodAndParsedIntoProperComponent(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '-', 2);
        $this->assertEquals(new Subs(1, 2), $result);
    }

    public function testArithmeticMultiplicationShouldBeUnderstoodAndParsedIntoProperComponent(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '*', 2);
        $this->assertEquals(new Mults(1, 2), $result);
    }

    public function testArithmeticDivisionShouldBeUnderstoodAndParsedIntoProperComponent(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '/', 2);
        $this->assertEquals(new Divs(1, 2), $result);
    }

    public function testArithmeticModuloShouldBeUnderstoodAndParsedIntoProperComponent(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '%', 2);
        $this->assertEquals(new Mods(1, 2), $result);
    }

    public function testAdditionsShouldBeDoneAfterMultiplications(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '+', 2, '*', 3);
        $this->assertEquals(new Adds(1, new Mults(2, 3)), $result);
    }

    public function testSubtractionsShouldBeDoneAfterMultiplications(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '-', 2, '*', 3);
        $this->assertEquals(new Subs(1, new Mults(2, 3)), $result);
    }

    public function testSubtractionsStillHappenBeforeAdditionIfAppearingFirst(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '-', 2, '+', 3);
        $this->assertEquals(new Adds(new Subs(1, 2), 3), $result);
    }

    public function testAdditionsShouldBeDoneAfterDivisions(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '+', 2, '/', 3);
        $this->assertEquals(new Adds(1, new Divs(2, 3)), $result);
    }

    public function testSubtractionsShouldBeDoneAfterDivisions(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '-', 2, '/', 3);
        $this->assertEquals(new Subs(1, new Divs(2, 3)), $result);
    }

    public function testAdditionsShouldBeDoneAfterModulo(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '+', 2, '%', 3);
        $this->assertEquals(new Adds(1, new Mods(2, 3)), $result);
    }

    public function testSubtractionsShouldBeDoneAfterModulo(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(1, '-', 2, '%', 3);
        $this->assertEquals(new Subs(1, new Mods(2, 3)), $result);
    }

    public function testAdditionWithIncorrectOperandsTriggerErrorStart(): void
    {
        $builder = new ExpressionBuilder();

        $this->expectException(ExpressionBuildingMissingOperandForFoundTokenException::class);

        $builder->build('+', 2);
    }

    public function testAdditionWithIncorrectOperandsTriggerErrorEnd(): void
    {
        $builder = new ExpressionBuilder();

        $this->expectException(ExpressionBuildingMissingOperandForFoundTokenException::class);

        $builder->build(2, '+');
    }

    public function testSubtractionWithIncorrectOperandsTriggerErrorStart(): void
    {
        $builder = new ExpressionBuilder();

        $this->expectException(ExpressionBuildingMissingOperandForFoundTokenException::class);

        $builder->build('-', 2);
    }

    public function testSubtractionWithIncorrectOperandsTriggerErrorEnd(): void
    {
        $builder = new ExpressionBuilder();

        $this->expectException(ExpressionBuildingMissingOperandForFoundTokenException::class);

        $builder->build('-', 2);
    }

    public function testNotPicksNextTokenAsOperand(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build('!', true);
        $this->assertEquals(new Nots(true, doubled: false), $result);
    }

    public function testNotChecksIfNextNotExistsToDoubleNot(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build('!', '!', true);
        $this->assertEquals(new Nots(true, doubled: true), $result);
    }

    public function testExponentTokenConvertedAsExpected(): void
    {
        $builder = new ExpressionBuilder();

        $result = $builder->build(10, '**', 2);
        $this->assertEquals(new Exps(10, 2), $result);
    }
}