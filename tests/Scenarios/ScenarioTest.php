<?php

namespace CrazyCodeGen\Tests\Scenarios;

use CrazyCodeGen\Definitions\Structures\ArgDef;
use CrazyCodeGen\Definitions\Values\Variable;
use CrazyCodeGen\Factories\DefinitionFactory;
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

        $resultingCode = $renderer->render($exp, $rules, new RenderContext());

        $this->assertEquals('$foo = \'hello\' . (($bar + 1) * 3.1416)', $resultingCode);
    }

    public function testWeCanBuildComplexExpressionsFromTokens(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $eb = new ExpressionBuilder();
        $exp = $eb->build('hello', '.', '(', '(', 17, '+', 1, ')', '*', 3.1416, ')');

        $resultingCode = $renderer->render($exp, $rules, new RenderContext());

        $this->assertEquals('\'hello\' . ((17 + 1) * 3.1416)', $resultingCode);
    }

    public function testWeCanCallFunctionsByNameWithBasicArguments(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $ef = new ExpressionFactory();
        $exp = $ef->calls('array_merge', [1, 2, 3]);

        $resultingCode = $renderer->render($exp, $rules, new RenderContext());

        $this->assertEquals('array_merge(1, 2, 3)', $resultingCode);
    }

    public function testWeCanCallFunctionsByNameWithExpressions(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $df = new DefinitionFactory();
        $ef = new ExpressionFactory();
        $exp = $ef->calls('array_merge', [$ef->adds(1, 2), $df->var('foo'), 'hello']);

        $resultingCode = $renderer->render($exp, $rules, new RenderContext());

        $this->assertEquals('array_merge(1 + 2, $foo, \'hello\')', $resultingCode);
    }

    public function testWeCanCallFunctionsByFunctionDefinition(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $df = new DefinitionFactory();
        $ef = new ExpressionFactory();
        $exp = $ef->calls($df->funcDef(name: 'getGlobalKernel'));

        $resultingCode = $renderer->render($exp, $rules, new RenderContext());

        $this->assertEquals('getGlobalKernel()', $resultingCode);
    }

    public function testWeCanDefineFunctions(): void
    {
        $renderer = new Renderer();
        $rules = new RenderingRules();

        $df = new DefinitionFactory();
        $ef = new ExpressionFactory();
        $getGlobalKernel = $df->funcDef(
            name: 'getGlobalKernel',
            arguments: [
                new ArgDef(name: 'hello', type: 'string', defaultValue: 'world'),
            ],
            returnType: 'void',
            namespace: 'CrazyCodeGen\\Tests',
            body: [
                $ef->adds(1, 2),
                $ef->mults(2, 3),
            ]
        );

        $resultingCode = $renderer->render($getGlobalKernel, $rules, new RenderContext());

        $this->assertEquals(<<<EOS
            namespace CrazyCodeGen\Tests;
            
            function getGlobalKernel(string \$hello = 'world'): void
            {
                1 + 2;
                2 * 3;
            }
            EOS,
            $resultingCode
        );
    }
}