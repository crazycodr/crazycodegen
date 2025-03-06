<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ArgumentListDeclarationRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArgumentListDeclarationTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioHasStartAndEndParenthesisAndTokensFromArgumentAndNoTrailingComma()
    {
        $token = new ArgumentListDeclarationTokenGroup(
            [
                new ArgumentDeclarationTokenGroup('foo'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = true;

        $this->assertEquals(
            '($foo)',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioHasCommaAndSpaceAfterEachArgumentExceptLast()
    {
        $token = new ArgumentListDeclarationTokenGroup(
            [
                new ArgumentDeclarationTokenGroup('foo'),
                new ArgumentDeclarationTokenGroup('bar'),
                new ArgumentDeclarationTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = true;

        $this->assertEquals(
            '($foo, $bar, $baz)',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioHasIndentsForEachArgumentAndCommaAfterEachButNoSpacesExceptLastWhenConfiguredAsSo()
    {
        $token = new ArgumentListDeclarationTokenGroup(
            [
                new ArgumentDeclarationTokenGroup('foo'),
                new ArgumentDeclarationTokenGroup('bar'),
                new ArgumentDeclarationTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;

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
        $token = new ArgumentListDeclarationTokenGroup(
            [
                new ArgumentDeclarationTokenGroup('foo'),
                new ArgumentDeclarationTokenGroup('bar'),
                new ArgumentDeclarationTokenGroup('baz'),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = true;

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
        $token = new ArgumentListDeclarationTokenGroup(
            [
                new ArgumentDeclarationTokenGroup('foo', type: 'string', defaultValue: 'Hello world'),
                new ArgumentDeclarationTokenGroup('bar', type: new MultiTypeTokenGroup(['string', 'bool', 'float'])),
                new ArgumentDeclarationTokenGroup('longerVarName', defaultValue: 1),
            ],
        );

        $rules = new RenderingRules();
        $rules->indentation = '    ';
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = true;

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
        $token = new ArgumentListDeclarationTokenGroup(
            [
                new ArgumentDeclarationTokenGroup('foo'),
            ],
        );

        $inlineScenario = $token->renderInlineScenario(new RenderContext(), new RenderingRules());
        $defaultScenario = $token->render(new RenderContext(), new RenderingRules());

        $this->assertEquals($inlineScenario, $defaultScenario);
    }
}