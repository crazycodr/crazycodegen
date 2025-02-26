<?php

namespace CrazyCodeGen\Tests\Scenarios;

use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Factories\ExpressionBuilder;
use CrazyCodeGen\Factories\ExpressionFactory;
use CrazyCodeGen\Renderers\RenderContext;
use CrazyCodeGen\Renderers\Renderer;
use CrazyCodeGen\Renderers\RenderingRules;
use PHPUnit\Framework\TestCase;

class ScenarioTest extends TestCase
{
    public function testWeCanRenderComplexArithmeticExpressions(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $ef = new ExpressionFactory();

        $exp = $ef->assigns(
            new Variable('foo'),
            $ef->concats(
                'hello',
                $ef->wraps(
                    $ef->mults(
                        $ef->wraps(
                            $ef->adds(
                                new Variable('bar'),
                                1,
                            ),
                        ),
                        3.1416,
                    ),
                ),
            ),
        );

        $resultingCode = $renderer->render($exp, $rules, $context);

        $this->assertEquals('$foo = \'hello\' . (($bar + 1) * 3.1416)', $resultingCode);
    }

    public function testWeCanBuildComplexExpressionsFromTokens(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();
        $context = new RenderContext();

        $eb = new ExpressionBuilder();
        $exp = $eb->build('hello', '.', '(', '(', 17, '+', 1, ')', '*', 3.1416, ')');

        $resultingCode = $renderer->render($exp, $rules, $context);

        $this->assertEquals('\'hello\' . ((17 + 1) * 3.1416)', $resultingCode);
    }
}