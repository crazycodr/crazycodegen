<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\ArgumentListRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParameterListTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParameterTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArrayTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ClassTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NewInstanceTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NewInstanceTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineNewKeywordClassnameAndEmptyParenthesesArePresent()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineStringClassNameIsConvertedAndOutputAsExpected()
    {
        $token = new NewInstanceTokenGroup(
            class: 'foo'
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineClassTokenGroupGetConvertedToSimpleClassName()
    {
        $token = new NewInstanceTokenGroup(
            class: new ClassTokenGroup(name: 'foo', namespace: new NamespaceTokenGroup('CrazyCodeGen\Tests')),
        );

        $this->assertEquals(
            <<<'EOS'
            new CrazyCodeGen\Tests\foo()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineClassTokenGroupGetShortNameRenderedWhenClassIsImportedInContext()
    {
        $token = new NewInstanceTokenGroup(
            class: new ClassTokenGroup(name: 'foo', namespace: new NamespaceTokenGroup('CrazyCodeGen\Tests')),
        );

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\Tests\foo';

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->renderInlineScenario($context, new RenderingRules()))
        );
    }

    public function testInlineRendersStringArgumentAsSingleToken()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenArgumentAsSingleToken()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupArgumentAsExpected()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: new VariableTokenGroup('bar'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: [new Token(1), new Token(2), new Token(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1, 2, 3)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: [new VariableTokenGroup('bar'), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar, $baz)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testInlineRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: [new ArrayTokenGroup([1, 2, 3]), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo([1, 2, 3], $baz)
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownNewKeywordClassnameAndEmptyParenthesesArePresent()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownStringClassNameIsConvertedAndOutputAsExpected()
    {
        $token = new NewInstanceTokenGroup(
            class: 'foo'
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownClassTokenGroupGetConvertedToSimpleClassName()
    {
        $token = new NewInstanceTokenGroup(
            class: new ClassTokenGroup(name: 'foo', namespace: new NamespaceTokenGroup('CrazyCodeGen\Tests')),
        );

        $this->assertEquals(
            <<<'EOS'
            new CrazyCodeGen\Tests\foo()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownClassTokenGroupGetShortNameRenderedWhenClassIsImportedInContext()
    {
        $token = new NewInstanceTokenGroup(
            class: new ClassTokenGroup(name: 'foo', namespace: new NamespaceTokenGroup('CrazyCodeGen\Tests')),
        );

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\Tests\foo';

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario($context, new RenderingRules()))
        );
    }

    public function testChopDownRendersStringArgumentAsSingleToken()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: 1,
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                1,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenArgumentAsSingleToken()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                1,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupArgumentAsExpected()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: new VariableTokenGroup('bar'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                $bar,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokensArgumentAsListOfItemsSeparatedByCommasNewLinesAndEverythingIsIndented()
    {
        $token = new NewInstanceTokenGroup(
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
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: [new VariableTokenGroup('bar'), new VariableTokenGroup('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(
                $bar,
                $baz,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }

    public function testChopDownRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheChopDownVersions()
    {
        $token = new NewInstanceTokenGroup(
            class: new Token('foo'),
            arguments: [new ArrayTokenGroup([1, 2, 3]), new VariableTokenGroup('baz')],
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
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), new RenderingRules()))
        );
    }
}
