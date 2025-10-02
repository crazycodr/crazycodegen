<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Contexts;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Contexts\StaticContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class StaticContextTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testYieldsStaticToken(): void
    {
        $context = new StaticContext();

        $this->assertEquals(
            'static',
            $this->renderTokensToString($context->getTokens(new RenderingContext())),
        );
    }

    public function testIsAccessedStatically(): void
    {
        $context = new StaticContext();

        $this->assertTrue($context->isAccessedStatically());
    }

    public function testStaticChainToReturnsChainOpWithSelfAndTarget(): void
    {
        $other = new PropertyDef('prop');

        $this->assertEquals(
            new ChainOp([new StaticContext(), $other]),
            StaticContext::to($other)
        );
    }
}
