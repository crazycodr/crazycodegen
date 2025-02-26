<?php

namespace CrazyCodeGen\Tests\Factories;

use CrazyCodeGen\Factories\DefinitionFactory;
use PHPUnit\Framework\TestCase;

class DefinitionFactoryTest extends TestCase
{
    public function testIntValReturnsTheExpectedObjectAndValue()
    {
        $factory = new DefinitionFactory();

        $target = $factory->intVal(13);
        $this->assertEquals(13, $target->value);

        $target = $factory->intVal(-777);
        $this->assertEquals(-777, $target->value);

        $target = $factory->intVal(0);
        $this->assertEquals(0, $target->value);
    }

    public function testFloatValReturnsTheExpectedObjectAndValue()
    {
        $factory = new DefinitionFactory();

        $target = $factory->floatVal(3.14159265);
        $this->assertEquals(3.14159265, $target->value);
    }

    public function testStringValReturnsTheExpectedObjectAndValue()
    {
        $factory = new DefinitionFactory();

        $target = $factory->stringVal('hello');
        $this->assertEquals('hello', $target->value);

        $target = $factory->stringVal("world");
        $this->assertEquals('world', $target->value);
    }

    public function testBoolValReturnsTheExpectedObjectAndValue()
    {
        $factory = new DefinitionFactory();

        $target = $factory->boolVal(true);
        $this->assertTrue($target->value);

        $target = $factory->boolVal(false);
        $this->assertFalse($target->value);
    }

    public function testVarReturnsTheExpectedObjectAndValue()
    {
        $factory = new DefinitionFactory();

        $target = $factory->var('foo');
        $this->assertEquals('foo', $target->name);
    }
}