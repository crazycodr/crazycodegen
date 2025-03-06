<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\ArgumentListRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArgumentListTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioHasStartAndEndParenthesisAndTokensFromArgumentAndNoTrailingComma()
    {
        $token = new ArgumentListTokenGroup(
            [
                new ArgumentTokenGroup('foo'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListRules();
        $rules->argumentLists->spacesAfterSeparator = 1;
        $rules->argumentLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            '($foo)',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioHasCommaAndSpaceAfterEachArgumentExceptLast()
    {
        $token = new ArgumentListTokenGroup(
            [
                new ArgumentTokenGroup('foo'),
                new ArgumentTokenGroup('bar'),
                new ArgumentTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListRules();
        $rules->argumentLists->spacesAfterSeparator = 1;
        $rules->argumentLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            '($foo, $bar, $baz)',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachButNoSpacesExceptLastWhenConfiguredAsSo()
    {
        $token = new ArgumentListTokenGroup(
            [
                new ArgumentTokenGroup('foo'),
                new ArgumentTokenGroup('bar'),
                new ArgumentTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListRules();
        $rules->argumentLists->spacesAfterSeparator = 1;
        $rules->argumentLists->addSeparatorToLastItem = false;

        $this->assertEquals(
            <<<EOS
            (
                \$foo,
                \$bar,
                \$baz
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachEventTheLastWhenConfiguredAsSo()
    {
        $token = new ArgumentListTokenGroup(
            [
                new ArgumentTokenGroup('foo'),
                new ArgumentTokenGroup('bar'),
                new ArgumentTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListRules();
        $rules->argumentLists->spacesAfterSeparator = 1;
        $rules->argumentLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            <<<EOS
            (
                \$foo,
                \$bar,
                \$baz,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownPadsTypesAndIdentifiersToAlignAllComponentsProperlyEvenIfTypeOrDefaultValueIsNotPresent()
    {
        $token = new ArgumentListTokenGroup(
            [
                new ArgumentTokenGroup('foo', type: 'string', defaultValue: 'Hello world'),
                new ArgumentTokenGroup('bar', type: new MultiTypeTokenGroup(['string', 'bool', 'float'])),
                new ArgumentTokenGroup('longerVarName', defaultValue: 1),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListRules();
        $rules->argumentLists->spacesAfterSeparator = 1;
        $rules->argumentLists->addSeparatorToLastItem = true;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForTypes = strlen('string|bool|float');
        $context->chopDown->paddingSpacesForIdentifiers = strlen('$longerVarName');

        $this->assertEquals(
            <<<EOS
            (
                string            \$foo           = 'Hello world',
                string|bool|float \$bar,
                                  \$longerVarName = 1,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario($context, $rules))
        );
    }

    public function testRenderReturnsInlineScenario(): void
    {
        $token = new ArgumentListTokenGroup(
            [
                new ArgumentTokenGroup('foo'),
            ],
        );

        $inlineScenario = $token->renderInlineScenario(new RenderContext(), new RenderingRules());
        $defaultScenario = $token->render(new RenderContext(), new RenderingRules());

        $this->assertEquals($inlineScenario, $defaultScenario);
    }
}