<?php

namespace CrazyCodeGen\Tests\Renderers;

use CrazyCodeGen\Definitions\Values\IntValue;
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
        $value = new IntValue(1);
        $adds = new Adds($variable, $value);

        $target = new Wraps($adds);

        $resultingCode = $renderer->render($target, $rules, $context);

        $this->assertEquals('($foo + 1)', $resultingCode);
    }
}