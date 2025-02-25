<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\IntValue;
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
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Concats($variable, $variable);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo . $foo', $resultingCode);
    }

    public function testConcatAssignsRendersPeriodEqualsAndSpacesAroundToken(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');
        $value = new IntValue(1);

        $target = new ConcatAssigns($variable, $value);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('$foo .= 1', $resultingCode);
    }
}