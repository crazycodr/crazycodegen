<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Expressions\Structures\Wraps;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class ExpressionStructureRendererTest extends TestCase
{
    public function testWrapsWillReturnParenthesesAndInnerTokensAsExpected(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $variable = new Variable('foo');

        $target = new Wraps(new Adds($variable, 1));
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('($foo + 1)', $resultingCode);

        $target = new Wraps('hello');
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('(\'hello\')', $resultingCode);

        $target = new Wraps(true);
        $resultingCode = $renderer->render($target, $rules, $context);
        $this->assertEquals('(true)', $resultingCode);
    }
}