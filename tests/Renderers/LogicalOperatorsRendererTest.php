<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Ands;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Nots;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Ors;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Xors;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class LogicalOperatorsRendererTest extends TestCase
{
    public function testNotsRendersExclamationAndTokensWithoutSpaces(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Nots($variable);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('!$foo', $resultingCode);
    }

    public function testDoubledNotsRendersDoubleExclamationAndTokensWithoutSpaces(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Nots($variable, doubled: true);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('!!$foo', $resultingCode);
    }

    public function testAndsRendersDoubleAmpersandsAndTokensWithSpacesAround(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Ands($variable, 1);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('$foo && 1', $resultingCode);

        $target = new Ands(2, $variable);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('2 && $foo', $resultingCode);
    }

    public function testTextBasedAndsRendersTextAndKeywordAndTokensWithSpacesAround(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Ands($variable, 1, textBased: true);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('$foo and 1', $resultingCode);

        $target = new Ands(2, $variable, textBased: true);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('2 and $foo', $resultingCode);
    }

    public function testOrsRendersDoubleAmpersandsAndTokensWithSpacesAround(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Ors($variable, 1);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('$foo || 1', $resultingCode);

        $target = new Ors(2, $variable);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('2 || $foo', $resultingCode);
    }

    public function testTextBasedOrsRendersTextAndKeywordAndTokensWithSpacesAround(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Ors($variable, 1, textBased: true);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('$foo or 1', $resultingCode);

        $target = new Ors(2, $variable, textBased: true);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('2 or $foo', $resultingCode);
    }

    public function testXorsRendersTextXorKeywordAndTokensWithSpacesAround(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Xors($variable, 1);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('$foo xor 1', $resultingCode);

        $target = new Xors(2, $variable);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('2 xor $foo', $resultingCode);
    }
}