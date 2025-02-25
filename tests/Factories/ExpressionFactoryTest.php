<?php

namespace CrazyCodeGen\Tests\Factories;

use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Factories\ExpressionFactory;
use PHPUnit\Framework\TestCase;

class ExpressionFactoryTest extends TestCase
{
    public function testAssignsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->assigns($foo, $bar);

        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testDecrementsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');

        $target = $factory->decrements($foo);
        $this->assertSame($foo, $target->operand);
        $this->assertFalse($target->pre);

        $target = $factory->decrements($foo, preDecrement: true);
        $this->assertSame($foo, $target->operand);
        $this->assertTrue($target->pre);
    }

    public function testIncrementsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');

        $target = $factory->increments($foo);
        $this->assertSame($foo, $target->operand);
        $this->assertFalse($target->pre);

        $target = $factory->increments($foo, preIncrement: true);
        $this->assertSame($foo, $target->operand);
        $this->assertTrue($target->pre);
    }

    public function testEqualsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->equals($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertFalse($target->soft);

        $target = $factory->equals($foo, $bar, softEquals: true);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertTrue($target->soft);
    }

    public function testNotEqualsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->notEquals($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertFalse($target->soft);
        $this->assertFalse($target->useLtGt);

        $target = $factory->notEquals($foo, $bar, softEquals: true);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertTrue($target->soft);
        $this->assertFalse($target->useLtGt);

        $target = $factory->notEquals($foo, $bar, useLtGt: true);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertFalse($target->soft);
        $this->assertTrue($target->useLtGt);
    }

    public function testAndsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->ands($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertFalse($target->textBased);

        $target = $factory->ands($foo, $bar, textVersion: true);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertTrue($target->textBased);
    }

    public function testOrsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->ors($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertFalse($target->textBased);

        $target = $factory->ors($foo, $bar, textVersion: true);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
        $this->assertTrue($target->textBased);
    }

    public function testXorsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->xors($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testNotsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');

        $target = $factory->nots($foo);
        $this->assertSame($foo, $target->operand);
        $this->assertFalse($target->doubled);

        $target = $factory->nots($foo, doubled: true);
        $this->assertSame($foo, $target->operand);
        $this->assertTrue($target->doubled);
    }

    public function testGtReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->gt($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testGteReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->gte($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testLtReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->lt($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testLteReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->lte($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testConcatsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->concats($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testConcatAssignsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->concatAssigns($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testAddsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->adds($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testSubsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->subs($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testMultsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->mults($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testDivsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->divs($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testModsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->mods($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }

    public function testExpsReturnsExpectedObject()
    {
        $factory = new ExpressionFactory();

        $foo = new Variable('foo');
        $bar = new Variable('bar');

        $target = $factory->exps($foo, $bar);
        $this->assertSame($foo, $target->left);
        $this->assertSame($bar, $target->right);
    }
}