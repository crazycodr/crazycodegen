<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expressions\Operations\Call;
use CrazyCodeGen\Definition\Expressions\Operations\Chain;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ChainTest extends TestCase
{
    use TokenFunctions;

    public function testInlineChainsItemsTogetherWithAccessTokens()
    {
        $token = new Chain(
            chain: [
                new Token('$foo'),
                new Token('bar'),
                new Token('baz'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo->bar->baz
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineConvertsStringsToTokens()
    {
        $token = new Chain(
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
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineConvertsTokenToArrayOfTokens()
    {
        $token = new Chain(
            chain: new Token('$foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineConvertsTokenGroupToArrayOfTokenGroups()
    {
        $token = new Chain(
            chain: new VariableDef('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroups()
    {
        $token = new Chain(
            chain: [
                new VariableDef('foo'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineTransformsPropertyTokenGroupToTokenAndLosesDollarSignBecauseConsideredAsAccess()
    {
        $token = new Chain(
            chain: [
                new PropertyDef(name: 'foo', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            foo
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersThisRefTokenGroupAndAdditionalPropertiesProperly()
    {
        $token = new Chain(
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
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersStaticAccessTokensInsteadOfMemberTokensWhenItFindsTokenGroupThatExposesStaticContext()
    {
        $token = new Chain(
            chain: [
                new ParentContext(),
                new Call(name: 'setUp'),
                new PropertyDef(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            parent::setUp()->bar
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownChainsItemsTogetherWithAccessTokensAndOnlyFromThirdItemsDoWeGetNewLinesAndIndents()
    {
        $token = new Chain(
            chain: [
                new Token('$foo'),
                new Token('bar'),
                new Token('baz'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo->bar
                ->baz
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownConvertsStringsToTokensAndOnlyFromThirdItemsDoWeGetNewLinesAndIndents()
    {
        $token = new Chain(
            chain: [
                '$foo',
                'bar',
                'baz',
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo->bar
                ->baz
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownConvertsTokenToArrayOfTokens()
    {
        $token = new Chain(
            chain: new Token('$foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownConvertsTokenGroupToArrayOfTokenGroups()
    {
        $token = new Chain(
            chain: new VariableDef('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroups()
    {
        $token = new Chain(
            chain: [
                new VariableDef('foo'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownTransformsPropertyTokenGroupToTokenAndLosesDollarSignBecauseConsideredAsAccess()
    {
        $token = new Chain(
            chain: [
                new PropertyDef(name: 'foo', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            foo
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersThisRefTokenGroupAndAdditionalPropertiesProperlyAndOnlyFromThirdItemsDoWeGetNewLinesAndIndents()
    {
        $token = new Chain(
            chain: [
                new ThisContext(),
                new PropertyDef(name: 'foo', type: 'int'),
                new PropertyDef(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $this->foo
                 ->bar
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersStaticAccessTokensInsteadOfMemberTokensWhenItFindsTokenGroupThatExposesStaticContext()
    {
        $token = new Chain(
            chain: [
                new ParentContext(),
                new Call(name: 'setUp'),
                new PropertyDef(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            parent::setUp()
                  ->bar
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
