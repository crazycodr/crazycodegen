<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ChainOpTest extends TestCase
{
    use TokenFunctions;

    public function testChainsItemsTogetherWithAccessTokens()
    {
        $token = new ChainOp(
            chain: [
                new Expression('$foo'),
                new Expression('bar'),
                new Expression('baz'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo->bar->baz
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testConvertsStringsToExpressions()
    {
        $token = new ChainOp(
            chain: [
                '$foo',
                'bar',
                'baz',
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo->bar->baz
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testRendersTokenGroups()
    {
        $token = new ChainOp(
            chain: [
                new VariableDef('foo'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testTransformsPropertyTokenGroupToTokenAndLosesDollarSignBecauseConsideredAsAccess()
    {
        $token = new ChainOp(
            chain: [
                new PropertyDef(name: 'foo', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            foo
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testRendersThisRefTokenGroupAndAdditionalPropertiesProperly()
    {
        $token = new ChainOp(
            chain: [
                new ThisContext(),
                new PropertyDef(name: 'foo', type: 'int'),
                new PropertyDef(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $this->foo->bar
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testRendersStaticAccessTokensInsteadOfMemberTokensWhenItFindsTokenGroupThatExposesStaticContext()
    {
        $token = new ChainOp(
            chain: [
                new ParentContext(),
                new CallOp(subject: 'setUp'),
                new PropertyDef(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            parent::setUp()->bar
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
