<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Expressions\Operators\Strings\ConcatAssigns;
use CrazyCodeGen\Expressions\Operators\Strings\Concats;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class StringsRendererTest extends TestCase
{
    public function testConcatsRendersPeriodAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Concats($variable, 'hello');
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo . \'hello\'', $resultingCode);

        $target = new Concats('hello', $variable);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('\'hello\' . $foo', $resultingCode);

        $target = new Concats('hello', 'hello');
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('\'hello\' . \'hello\'', $resultingCode);
    }

    public function testConcatAssignsRendersPeriodEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new ConcatAssigns($variable, 1);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('$foo .= 1', $resultingCode);
    }
}