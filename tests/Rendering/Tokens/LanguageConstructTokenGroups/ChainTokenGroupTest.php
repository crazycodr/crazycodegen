<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Definitions\Structures\PropertyDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ChainTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\FunctionCallTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParentRefTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ThisRefTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ChainTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineChainsItemsTogetherWithAccessTokens()
    {
        $token = new ChainTokenGroup(
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
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineConvertsStringsToTokens()
    {
        $token = new ChainTokenGroup(
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
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineConvertsTokenToArrayOfTokens()
    {
        $token = new ChainTokenGroup(
            chain: new Token('$foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineConvertsTokenGroupToArrayOfTokenGroups()
    {
        $token = new ChainTokenGroup(
            chain: new VariableTokenGroup('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroups()
    {
        $token = new ChainTokenGroup(
            chain: [
                new VariableTokenGroup('foo'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineTransformsPropertyTokenGroupToTokenAndLosesDollarSignBecauseConsideredAsAccess()
    {
        $token = new ChainTokenGroup(
            chain: [
                new PropertyDefinition(name: 'foo', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            foo
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersThisRefTokenGroupAndAdditionalPropertiesProperly()
    {
        $token = new ChainTokenGroup(
            chain: [
                new ThisRefTokenGroup(),
                new PropertyDefinition(name: 'foo', type: 'int'),
                new PropertyDefinition(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $this->foo->bar
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersStaticAccessTokensInsteadOfMemberTokensWhenItFindsTokenGroupThatExposesStaticContext()
    {
        $token = new ChainTokenGroup(
            chain: [
                new ParentRefTokenGroup(),
                new FunctionCallTokenGroup(name: 'setUp'),
                new PropertyDefinition(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            parent::setUp()->bar
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownChainsItemsTogetherWithAccessTokensAndOnlyFromThirdItemsDoWeGetNewLinesAndIndents()
    {
        $token = new ChainTokenGroup(
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
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownConvertsStringsToTokensAndOnlyFromThirdItemsDoWeGetNewLinesAndIndents()
    {
        $token = new ChainTokenGroup(
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
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownConvertsTokenToArrayOfTokens()
    {
        $token = new ChainTokenGroup(
            chain: new Token('$foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownConvertsTokenGroupToArrayOfTokenGroups()
    {
        $token = new ChainTokenGroup(
            chain: new VariableTokenGroup('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroups()
    {
        $token = new ChainTokenGroup(
            chain: [
                new VariableTokenGroup('foo'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $foo
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownTransformsPropertyTokenGroupToTokenAndLosesDollarSignBecauseConsideredAsAccess()
    {
        $token = new ChainTokenGroup(
            chain: [
                new PropertyDefinition(name: 'foo', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            foo
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersThisRefTokenGroupAndAdditionalPropertiesProperlyAndOnlyFromThirdItemsDoWeGetNewLinesAndIndents()
    {
        $token = new ChainTokenGroup(
            chain: [
                new ThisRefTokenGroup(),
                new PropertyDefinition(name: 'foo', type: 'int'),
                new PropertyDefinition(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            $this->foo
                 ->bar
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersStaticAccessTokensInsteadOfMemberTokensWhenItFindsTokenGroupThatExposesStaticContext()
    {
        $token = new ChainTokenGroup(
            chain: [
                new ParentRefTokenGroup(),
                new FunctionCallTokenGroup(name: 'setUp'),
                new PropertyDefinition(name: 'bar', type: 'int'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            parent::setUp()
                  ->bar
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }
}
