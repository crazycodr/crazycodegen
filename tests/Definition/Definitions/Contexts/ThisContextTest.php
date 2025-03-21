<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ThisContextTest extends TestCase
{
    use TokenFunctions;

    public function testYieldsDollarThisToken(): void
    {
        $context = new ThisContext();

        $this->assertEquals(
            '$this',
            $this->renderTokensToString($context->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testStaticChainToReturnsChainOpWithSelfAndTarget(): void
    {
        $other = new PropertyDef('prop');

        $this->assertEquals(
            new ChainOp([new ThisContext(), $other]),
            ThisContext::to($other)
        );
    }
}
