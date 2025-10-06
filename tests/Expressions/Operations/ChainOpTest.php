<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ChainOpTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testChainsItemsTogetherWithAccessTokens(): void
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
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testConvertsStringsToExpressions(): void
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
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersTokenGroups(): void
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
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTransformsPropertyTokenGroupToTokenAndLosesDollarSignBecauseConsideredAsAccess(): void
    {
        $token = new ChainOp(
            chain: [
                new PropertyDef(name: 'foo', type: BuiltInTypeSpec::intType()),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            foo
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersThisRefTokenGroupAndAdditionalPropertiesProperly(): void
    {
        $token = new ChainOp(
            chain: [
                new ThisContext(),
                new PropertyDef(name: 'foo', type: BuiltInTypeSpec::intType()),
                new PropertyDef(name: 'bar', type: BuiltInTypeSpec::intType()),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $this->foo->bar
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersStaticAccessTokensInsteadOfMemberTokensWhenItFindsTokenGroupThatExposesStaticContext(): void
    {
        $token = new ChainOp(
            chain: [
                new ParentContext(),
                new CallOp(subject: 'setUp'),
                new PropertyDef(name: 'bar', type: BuiltInTypeSpec::intType()),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            parent::setUp()->bar
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
