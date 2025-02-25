<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\IntValue;
use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Expressions\Operators\Assigns\Assigns;
use CrazyCodeGen\Expressions\Operators\Assigns\Decrements;
use CrazyCodeGen\Expressions\Operators\Assigns\Increments;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class AssignsRendererTest extends TestCase
{
    public function testAssignsRendersEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Assigns($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo = 1', $resultingCode);
    }

    public function testIncrementsRendersDoubledPlusWithoutSpacesAroundTokens(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Increments($variable);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo++', $resultingCode);
    }

    public function testPreIncrementsRendersDoubledPlusWithoutSpacesAroundTokens(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Increments($variable, pre: true);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('++$foo', $resultingCode);
    }

    public function testDecrementsRendersDoubledPlusWithoutSpacesAroundTokens(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Decrements($variable);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo--', $resultingCode);
    }

    public function testPreDecrementsRendersDoubledPlusWithoutSpacesAroundTokens(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Decrements($variable, pre: true);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('--$foo', $resultingCode);
    }
}