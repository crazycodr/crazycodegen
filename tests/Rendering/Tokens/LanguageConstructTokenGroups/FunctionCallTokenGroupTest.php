<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArrayTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MethodTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\FunctionCallTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FunctionCallTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineSubjectArrowAndFunctionRenderedFromTokensAndParenthesesArePresent()
    {
        $token = new FunctionCallTokenGroup(
            name: new Token('setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineSubjectAndFunctionAreConvertedToTokensWhenStrings()
    {
        $token = new FunctionCallTokenGroup(
            name: 'setUp',
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineFunctionConvertedToTokenWhenMethodTokenGroupPassedIn()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersStringArgumentAsSingleToken()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenArgumentAsSingleToken()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupArgumentAsExpected()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: new VariableTokenGroup('bar'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp($bar)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: [new Token(1), new Token(2), new Token(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1, 2, 3)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: [new VariableTokenGroup('bar'), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp($bar, $baz)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: [new ArrayTokenGroup([1, 2, 3]), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp([1, 2, 3], $baz)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownSubjectArrowAndFunctionRenderedFromTokensAndParenthesesArePresent()
    {
        $token = new FunctionCallTokenGroup(
            name: new Token('setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownSubjectAndFunctionAreConvertedToTokensWhenStrings()
    {
        $token = new FunctionCallTokenGroup(
            name: 'setUp',
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownFunctionConvertedToTokenWhenMethodTokenGroupPassedIn()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersStringArgumentAsSingleToken()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                1,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenArgumentAsSingleToken()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                1,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupArgumentAsExpected()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: new VariableTokenGroup('bar'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                $bar,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: [new Token(1), new Token(2), new Token(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                1,
                2,
                3,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: [new VariableTokenGroup('bar'), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                $bar,
                $baz,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheChopDownVersions()
    {
        $token = new FunctionCallTokenGroup(
            name: new MethodTokenGroup(name: 'setUp'),
            arguments: [new ArrayTokenGroup([1, 2, 3]), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                [
                    1,
                    2,
                    3,
                ],
                $baz,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }
}
