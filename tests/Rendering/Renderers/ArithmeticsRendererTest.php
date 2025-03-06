<?php

namespace CrazyCodeGen\Tests\Rendering\Renderers;

use CrazyCodeGen\Definition\Definitions\Values\Variable;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Divs;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Exps;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Mods;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Mults;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Subs;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Renderer;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use PHPUnit\Framework\TestCase;

class ArithmeticsRendererTest extends TestCase
{
    public function testAddsRendersPlusAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Adds($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo + 1', $resultingCode);

        $target = new Adds(1, 2);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('1 + 2', $resultingCode);
    }

    public function testSubsRendersMinusAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Subs($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo - 1', $resultingCode);

        $target = new Subs(2, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('2 - 1', $resultingCode);
    }

    public function testMultsRendersAsteriskAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Mults($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo * 1', $resultingCode);

        $target = new Mults(3, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('3 * 1', $resultingCode);
    }

    public function testDivsRendersSlashAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Divs($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo / 1', $resultingCode);

        $target = new Divs(4, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('4 / 1', $resultingCode);
    }

    public function testModsRendersPercentAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Mods($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo % 1', $resultingCode);

        $target = new Mods(2, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('2 % 1', $resultingCode);
    }

    public function testExpsRendersDoubleAsterisksAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Exps($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo ** 1', $resultingCode);

        $target = new Exps(10, 2);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('10 ** 2', $resultingCode);
    }
}