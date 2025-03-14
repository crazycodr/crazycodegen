<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Definitions\Contexts\SelfContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class SelfContextTest extends TestCase
{
    use TokenFunctions;

    public function testYieldsSelfToken(): void
    {
        $context = new SelfContext();

        $this->assertEquals(
            'self',
            $this->renderTokensToString($context->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testIsAccessedStatically(): void
    {
        $context = new SelfContext();

        $this->assertTrue($context->isAccessedStatically());
    }

    public function testStaticChainToReturnsChainOpWithSelfAndTarget(): void
    {
        $other = new PropertyDef('prop');

        $this->assertEquals(
            new ChainOp([new SelfContext(), $other]),
            SelfContext::to($other)
        );
    }
}
