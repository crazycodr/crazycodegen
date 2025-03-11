<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\MultiTypeDefinition;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDefinition;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\ArgumentListRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParameterListDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioHasStartAndEndParenthesisAndTokensFromArgumentAndNoTrailingComma()
    {
        $token = new ParameterListDefinition(
            [
                new ParameterDefinition('foo'),
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
        $token = new ParameterListDefinition(
            [
                new ParameterDefinition('foo'),
                new ParameterDefinition('bar'),
                new ParameterDefinition('baz'),
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
        $token = new ParameterListDefinition(
            [
                new ParameterDefinition('foo'),
                new ParameterDefinition('bar'),
                new ParameterDefinition('baz'),
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
        $token = new ParameterListDefinition(
            [
                new ParameterDefinition('foo'),
                new ParameterDefinition('bar'),
                new ParameterDefinition('baz'),
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
        $token = new ParameterListDefinition(
            [
                new ParameterDefinition('foo', type: 'string', defaultValue: 'Hello world'),
                new ParameterDefinition('bar', type: new MultiTypeDefinition(['string', 'bool', 'float'])),
                new ParameterDefinition('longerVarName', defaultValue: 1),
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
        $token = new ParameterListDefinition(
            [
                new ParameterDefinition('foo'),
            ],
        );

        $inlineScenario = $token->renderInlineScenario(new RenderContext(), new RenderingRules());
        $defaultScenario = $token->render(new RenderContext(), new RenderingRules());

        $this->assertEquals($inlineScenario, $defaultScenario);
    }
}
