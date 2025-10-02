<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Contexts;

use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParentContextTest extends TestCase
{
    use TokenFunctions;

    public function testYieldsParentToken(): void
    {
        $context = new ParentContext();

        $this->assertEquals(
            'parent',
            $this->renderTokensToString($context->getSimpleTokens(new TokenizationContext())),
        );
    }

    public function testIsAccessedStatically(): void
    {
        $context = new ParentContext();

        $this->assertTrue($context->isAccessedStatically());
    }

    public function testStaticChainToReturnsChainOpWithSelfAndTarget(): void
    {
        $other = new PropertyDef('prop');

        $this->assertEquals(
            new ChainOp([new ParentContext(), $other]),
            ParentContext::to($other)
        );
    }
}
