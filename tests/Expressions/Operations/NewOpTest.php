<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expressions\Operations\NewOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NewOpTest extends TestCase
{
    use TokenFunctions;

    public function testInlineNewKeywordClassnameAndEmptyParenthesesArePresent()
    {
        $token = new NewOp(
            class: new Token('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineStringClassNameIsConvertedAndOutputAsExpected()
    {
        $token = new NewOp(
            class: 'foo'
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineClassTokenizerGetConvertedToSimpleClassName()
    {
        $token = new NewOp(
            class: new ClassDef(name: 'foo', namespace: new NamespaceDef('CrazyCodeGen\Tests')),
        );

        $this->assertEquals(
            <<<'EOS'
            new CrazyCodeGen\Tests\foo()
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineClassTokenizerGetShortNameRenderedWhenClassIsImportedInContext()
    {
        $token = new NewOp(
            class: new ClassDef(name: 'foo', namespace: new NamespaceDef('CrazyCodeGen\Tests')),
        );

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\Tests\foo';

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getInlineTokens($context, new RenderingRules()))
        );
    }

    public function testInlineRendersStringArgumentAsSingleToken()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [1],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenizerArgumentAsExpected()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new VariableDef('bar')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new Token(1), new Token(2), new Token(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1, 2, 3)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenizersArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new VariableDef('bar'), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar, $baz)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenizersArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new ArrayVal([1, 2, 3]), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo([1, 2, 3], $baz)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownNewKeywordClassnameAndEmptyParenthesesArePresent()
    {
        $token = new NewOp(
            class: new Token('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownStringClassNameIsConvertedAndOutputAsExpected()
    {
        $token = new NewOp(
            class: 'foo'
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownClassTokenizerGetConvertedToSimpleClassName()
    {
        $token = new NewOp(
            class: new ClassDef(name: 'foo', namespace: new NamespaceDef('CrazyCodeGen\Tests')),
        );

        $this->assertEquals(
            <<<'EOS'
            new CrazyCodeGen\Tests\foo()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownClassTokenizerGetShortNameRenderedWhenClassIsImportedInContext()
    {
        $token = new NewOp(
            class: new ClassDef(name: 'foo', namespace: new NamespaceDef('CrazyCodeGen\Tests')),
        );

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\Tests\foo';

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getChopDownTokens($context, new RenderingRules()))
        );
    }

    public function testChopDownRendersStringArgumentAsSingleToken()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [1],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                1,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenizerArgumentAsExpected()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new VariableDef('bar')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                $bar,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokensArgumentAsListOfItemsSeparatedByCommasNewLinesAndEverythingIsIndented()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new Token(1), new Token(2), new Token(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                1,
                2,
                3,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenizersArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new VariableDef('bar'), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                $bar,
                $baz,
            )
            EOS,
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenizersArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheChopDownVersions()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: [new ArrayVal([1, 2, 3]), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
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
