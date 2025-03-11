<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\ArgumentListRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParameterListTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParameterTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArgumentListTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioHasStartAndEndParenthesisAndTokensFromArgumentAndNoTrailingComma()
    {
        $token = new ParameterListTokenGroup(
            [
                new ParameterTokenGroup('foo'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            '($foo)',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioHasCommaAndSpaceAfterEachArgumentExceptLast()
    {
        $token = new ParameterListTokenGroup(
            [
                new ParameterTokenGroup('foo'),
                new ParameterTokenGroup('bar'),
                new ParameterTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            '($foo, $bar, $baz)',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachButNoSpacesExceptLastWhenConfiguredAsSo()
    {
        $token = new ParameterListTokenGroup(
            [
                new ParameterTokenGroup('foo'),
                new ParameterTokenGroup('bar'),
                new ParameterTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = false;

        $this->assertEquals(
            <<<'EOS'
            (
                $foo,
                $bar,
                $baz
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachEventTheLastWhenConfiguredAsSo()
    {
        $token = new ParameterListTokenGroup(
            [
                new ParameterTokenGroup('foo'),
                new ParameterTokenGroup('bar'),
                new ParameterTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            <<<'EOS'
            (
                $foo,
                $bar,
                $baz,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownPadsTypesAndIdentifiersToAlignAllComponentsProperlyEvenIfTypeOrDefaultValueIsNotPresent()
    {
        $token = new ParameterListTokenGroup(
            [
                new ParameterTokenGroup('foo', type: 'string', defaultValue: 'Hello world'),
                new ParameterTokenGroup('bar', type: new MultiTypeTokenGroup(['string', 'bool', 'float'])),
                new ParameterTokenGroup('longerVarName', defaultValue: 1),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForTypes = strlen('string|bool|float');
        $context->chopDown->paddingSpacesForIdentifiers = strlen('$longerVarName');

        $this->assertEquals(
            <<<'EOS'
            (
                string            $foo           = 'Hello world',
                string|bool|float $bar,
                                  $longerVarName = 1,
            )
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario($context, $rules))
        );
    }

    public function testRenderReturnsInlineScenario(): void
    {
        $token = new ParameterListTokenGroup(
            [
                new ParameterTokenGroup('foo'),
            ],
        );

        $inlineScenario = $token->renderInlineScenario(new RenderContext(), new RenderingRules());
        $defaultScenario = $token->render(new RenderContext(), new RenderingRules());

        $this->assertEquals($inlineScenario, $defaultScenario);
    }
}
