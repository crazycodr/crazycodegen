<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Operations\NewOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NewOpTest extends TestCase
{
    use TokenFunctions;

    public function testInlineNewKeywordClassnameAndEmptyParenthesesArePresent()
    {
        $token = new NewOp(
            class: new Expression('foo'),
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

    public function testInlineRendersStringArgumentAsSingleExpression()
    {
        $token = new NewOp(
            class: new Expression('foo'),
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
            class: new Expression('foo'),
            arguments: [new VariableDef('bar')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersExpressionsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [new Expression(1), new Expression(2), new Expression(3)],
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
            class: new Expression('foo'),
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
            class: new Expression('foo'),
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
            class: new Expression('foo'),
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

    public function testChopDownRendersStringArgumentAsSingleExpression()
    {
        $token = new NewOp(
            class: new Expression('foo'),
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
            class: new Expression('foo'),
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

    public function testChopDownRendersExpressionsArgumentAsListOfItemsSeparatedByCommasNewLinesAndEverythingIsIndented()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [new Expression(1), new Expression(2), new Expression(3)],
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
            class: new Expression('foo'),
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
            class: new Expression('foo'),
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
