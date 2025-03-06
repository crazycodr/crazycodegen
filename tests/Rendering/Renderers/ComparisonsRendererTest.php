<?php

namespace CrazyCodeGen\Tests\Rendering\Renderers;

use CrazyCodeGen\Definition\Definitions\Values\Variable;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\Equals;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsGreaterThan;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsGreaterThanOrEqualTo;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsLessThan;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\IsLessThanOrEqualTo;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\NotEquals;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Renderer;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use PHPUnit\Framework\TestCase;

class ComparisonsRendererTest extends TestCase
{
    public function testHardEqualsRendersTripleEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Equals($variable, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo === 1', $resultingCode);

        $target = new Equals(2, $variable);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 === $foo', $resultingCode);
    }

    public function testSoftEqualsRendersDoubleEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Equals($variable, 1, soft: true);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo == 1', $resultingCode);

        $target = new Equals(2, $variable, soft: true);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 == $foo', $resultingCode);
    }

    public function testHardNotEqualsRendersTripleEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new NotEquals($variable, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo !== 1', $resultingCode);

        $target = new NotEquals(2, $variable);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 !== $foo', $resultingCode);
    }

    public function testSoftNotEqualsRendersDoubleEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new NotEquals($variable, 1, soft: true);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo != 1', $resultingCode);

        $target = new NotEquals(2, $variable, soft: true);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 != $foo', $resultingCode);
    }

    public function testLtGtNotEqualsRendersLtAndGtAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new NotEquals($variable, 1, useLtGt: true);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo <> 1', $resultingCode);

        $target = new NotEquals(2, $variable, useLtGt: true);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 <> $foo', $resultingCode);
    }

    public function testIsLessThanRendersLtAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new IsLessThan($variable, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo < 1', $resultingCode);

        $target = new IsLessThan(2, $variable);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 < $foo', $resultingCode);
    }

    public function testIsLessThanOrEqualsRendersLtEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new IsLessThanOrEqualTo($variable, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo <= 1', $resultingCode);

        $target = new IsLessThanOrEqualTo(2, $variable);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 <= $foo', $resultingCode);
    }

    public function testIsMoreThanRendersLtAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new IsGreaterThan($variable, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo > 1', $resultingCode);

        $target = new IsGreaterThan(2, $variable);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 > $foo', $resultingCode);
    }

    public function testIsMoreThanOrEqualsRendersLtEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new IsGreaterThanOrEqualTo($variable, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('$foo >= 1', $resultingCode);

        $target = new IsGreaterThanOrEqualTo(2, 1);
        $resultingCode = $renderer->render($target, new RenderContext(), $rules);
        $this->assertEquals('2 >= 1', $resultingCode);
    }
}