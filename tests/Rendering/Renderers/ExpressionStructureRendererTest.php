<?php

namespace CrazyCodeGen\Tests\Rendering\Renderers;

use CrazyCodeGen\Definition\Definitions\Values\Variable;
use CrazyCodeGen\Definition\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Definition\Expressions\Structures\Wraps;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Renderer;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use PHPUnit\Framework\TestCase;

class ExpressionStructureRendererTest extends TestCase
{
    public function testWrapsWillReturnParenthesesAndInnerTokensAsExpected(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $variable = new Variable('foo');

        $target = new Wraps(new Adds($variable, 1));
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('($foo + 1)', $resultingCode);

        $target = new Wraps('hello');
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('(\'hello\')', $resultingCode);

        $target = new Wraps(true);
        $resultingCode = $renderer->render($target, $rules, new RenderContext());
        $this->assertEquals('(true)', $resultingCode);
    }
}