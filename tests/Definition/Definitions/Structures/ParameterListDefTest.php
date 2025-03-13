<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDef;
use CrazyCodeGen\Definition\Definitions\Structures\Types\MultiTypeDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\ArgumentListRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParameterListDefTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioHasStartAndEndParenthesisAndTokensFromArgumentAndNoTrailingComma()
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            '($foo)',
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioHasCommaAndSpaceAfterEachArgumentExceptLast()
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
                new ParameterDef('bar'),
                new ParameterDef('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->parameterLists = new ArgumentListRules();
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;

        $this->assertEquals(
            '($foo, $bar, $baz)',
            $this->renderTokensToString($token->getInlineTokens(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachButNoSpacesExceptLastWhenConfiguredAsSo()
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
                new ParameterDef('bar'),
                new ParameterDef('baz'),
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
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachEventTheLastWhenConfiguredAsSo()
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
                new ParameterDef('bar'),
                new ParameterDef('baz'),
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
            $this->renderTokensToString($token->getChopDownTokens(new RenderContext(), $rules))
        );
    }

    public function testChopDownPadsTypesAndIdentifiersToAlignAllComponentsProperlyEvenIfTypeOrDefaultValueIsNotPresent()
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo', type: 'string', defaultValue: 'Hello world'),
                new ParameterDef('bar', type: new MultiTypeDef(['string', 'bool', 'float'])),
                new ParameterDef('longerVarName', defaultValue: 1),
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
            $this->renderTokensToString($token->getChopDownTokens($context, $rules))
        );
    }

    public function testRenderReturnsInlineScenario(): void
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
            ],
        );

        $inlineScenario = $token->getInlineTokens(new RenderContext(), new RenderingRules());
        $defaultScenario = $token->getTokens(new RenderContext(), new RenderingRules());

        $this->assertEquals($inlineScenario, $defaultScenario);
    }
}
