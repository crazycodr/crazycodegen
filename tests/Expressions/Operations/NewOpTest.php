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

    public function testInlineClassTokenGroupGetConvertedToSimpleClassName()
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

    public function testInlineClassTokenGroupGetShortNameRenderedWhenClassIsImportedInContext()
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
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenArgumentAsSingleToken()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1)
            EOS,
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupArgumentAsExpected()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: new VariableDef('bar'),
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

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
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

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions()
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

    public function testChopDownClassTokenGroupGetConvertedToSimpleClassName()
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

    public function testChopDownClassTokenGroupGetShortNameRenderedWhenClassIsImportedInContext()
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
            arguments: 1,
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

    public function testChopDownRendersTokenArgumentAsSingleToken()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: new Token(1),
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

    public function testChopDownRendersTokenGroupArgumentAsExpected()
    {
        $token = new NewOp(
            class: new Token('foo'),
            arguments: new VariableDef('bar'),
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

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
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

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheChopDownVersions()
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
