<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ArgumentListDeclarationRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\FunctionDefinitionRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\FunctionDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;
use PHPUnit\Framework\TestCase;

class FunctionDefinitionTokenGroupTest extends TestCase
{
    use RenderTokensToStringTrait;

    public function testInlineDefinitionRendersFunctionKeyword()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction() {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNameOfFunction()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction() {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction() {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 1;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction () {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersArgumentListInlineAsExpectedBetweenParentheses()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction($foo, int $bar, bool $baz = true) {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 1;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction() : string {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 2;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction():  string {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(
            'function myFunction(): string  {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(
            'function myFunction()  {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            'function myFunction() {}',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnSameLineAndClosingBraceOnNextLineAsPerConfiguration()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            <<<EOS
            function myFunction() {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnNextLineWithClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            <<<EOS
            function myFunction()
            {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBracesOnSeparateLinesAsPerConfiguration()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(
            <<<EOS
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersFunctionKeyword()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNameOfFunction()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 1;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction (
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersArgumentListChopDownAsExpectedBetweenParentheses()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
                \$foo,
                int \$bar,
                bool \$baz = true
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 1;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
            ) : string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 2;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
            ):  string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(<<<EOS
            function myFunction(
            ): string  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(<<<EOS
            function myFunction(
            )  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningAndClosingBracesPositionIsNotRespectedAndAlwaysSameLineNextLine()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIsNeverWrap()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = new RenderingRules();
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::NEVER;
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(\$foo, int \$bar, bool \$baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesChopIfTooLongButItStillFits()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(\$foo, int \$bar, bool \$baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionIfArgumentsOnDifferentLinesChopIfTooLongAndItDoesNotFit()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = new RenderingRules();
        $rules->lineLength = 30;
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
                \$foo,
                int \$bar,
                bool \$baz = true
            ) {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionEvenIfArgumentsWouldFitButConfigurationForcesIt()
    {
        $token = new FunctionDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->argumentLists = new ArgumentListDeclarationRules();
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentLists->padTypeNames = false;
        $rules->argumentLists->padIdentifiers = false;
        $rules->functions = new FunctionDefinitionRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->functions->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functions->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functions->spacesBetweenReturnColonAndType = 1;
        $rules->functions->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            function myFunction(
                \$foo,
                int \$bar,
                bool \$baz = true
            ) {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}