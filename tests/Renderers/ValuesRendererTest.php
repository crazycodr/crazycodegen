<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\BoolValue;
use CrazyCodeGen\Definitions\Values\FloatValue;
use CrazyCodeGen\Definitions\Values\IntValue;
use CrazyCodeGen\Definitions\Values\StringValue;
use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class ValuesRendererTest extends TestCase
{
    public function testVariableRendersDollarSignAndName(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $target = new Variable('foo');
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('$foo', $resultingCode);
    }

    public function testBoolValueRendersTrueOrFalse(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $target1 = new BoolValue(true);
        $target2 = new BoolValue(false);

        $resultingCode = $renderer->render($target1, $rules, $context);
        $this->assertEquals('true', $resultingCode);

        $resultingCode = $renderer->render($target2, $rules, $context);
        $this->assertEquals('false', $resultingCode);
    }

    public function testFloatValueRendersValueWithPeriodAndDecimals(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $target = new FloatValue(3.14159265);

        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('3.14159265', $resultingCode);
    }

    public function testIntValueRendersValueAsExpected(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $target = new IntValue(PHP_INT_MAX);

        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals(PHP_INT_MAX, $resultingCode);
    }

    public function testStringValueRendersValueWithSingleQuotes(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $target = new StringValue('Hello world');

        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals("'Hello world'", $resultingCode);
    }

    public function testStringValueEscapesSingleQuotes(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $target = new StringValue('We can\'t do it, they\'re dead.');

        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals("'We can\'t do it, they\'re dead.'", $resultingCode);
    }
}