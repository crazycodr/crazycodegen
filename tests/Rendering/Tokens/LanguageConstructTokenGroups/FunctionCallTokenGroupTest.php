<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expressions\Operations\Call;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FunctionCallTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineSubjectArrowAndFunctionRenderedFromTokensAndParenthesesArePresent()
    {
        $token = new Call(
            name: new Token('setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineSubjectAndFunctionAreConvertedToTokensWhenStrings()
    {
        $token = new Call(
            name: 'setUp',
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineFunctionConvertedToTokenWhenMethodTokenGroupPassedIn()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersStringArgumentAsSingleToken()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenArgumentAsSingleToken()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupArgumentAsExpected()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: new VariableDef('bar'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp($bar)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: [new Token(1), new Token(2), new Token(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1, 2, 3)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: [new VariableDef('bar'), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp($bar, $baz)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: [new ArrayVal([1, 2, 3]), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp([1, 2, 3], $baz)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownSubjectArrowAndFunctionRenderedFromTokensAndParenthesesArePresent()
    {
        $token = new Call(
            name: new Token('setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownSubjectAndFunctionAreConvertedToTokensWhenStrings()
    {
        $token = new Call(
            name: 'setUp',
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownFunctionConvertedToTokenWhenMethodTokenGroupPassedIn()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersStringArgumentAsSingleToken()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                1,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenArgumentAsSingleToken()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                1,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupArgumentAsExpected()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: new VariableDef('bar'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                $bar,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
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
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: [new VariableDef('bar'), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(
                $bar,
                $baz,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheChopDownVersions()
    {
        $token = new Call(
            name: new MethodDef(name: 'setUp'),
            arguments: [new ArrayVal([1, 2, 3]), new VariableDef('baz')],
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
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
