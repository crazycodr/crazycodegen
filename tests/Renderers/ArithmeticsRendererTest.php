<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\IntValue;
use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Divs;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Exps;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Mods;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Mults;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class ArithmeticsRendererTest extends TestCase
{
    public function testAddsRendersPlusAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Adds($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo + 1', $resultingCode);
    }

    public function testSubsRendersMinusAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Adds($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo + 1', $resultingCode);
    }

    public function testMultsRendersAsteriskAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Mults($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo * 1', $resultingCode);
    }

    public function testDivsRendersSlashAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Divs($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo / 1', $resultingCode);
    }

    public function testModsRendersPercentAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Mods($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo % 1', $resultingCode);
    }

    public function testExpsRendersDoubleAsterisksAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new Exps($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo ** 1', $resultingCode);
    }
}