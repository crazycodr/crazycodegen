<?php

namespace CrazyCodeGen\Tests\Rendering\Renderers;

use CrazyCodeGen\Definition\Definitions\Values\Variable;
use CrazyCodeGen\Definition\Expressions\Operators\Assigns\Assigns;
use CrazyCodeGen\Definition\Expressions\Operators\Assigns\Decrements;
use CrazyCodeGen\Definition\Expressions\Operators\Assigns\Increments;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Renderer;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use PHPUnit\Framework\TestCase;

class AssignsRendererTest extends TestCase
{
    public function testAssignsRendersEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Assigns($variable, 1);
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