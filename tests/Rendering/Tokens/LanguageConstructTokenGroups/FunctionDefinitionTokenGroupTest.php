<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ArgumentListDefinitionRenderingRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\FunctionDefinitionRenderingRules;
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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 1;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 1;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 2;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 2;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 2;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 1;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 1;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 2;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 2;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 2;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->argumentsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->argumentsOnDifferentLines = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->argumentsOnDifferentLines = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules();
        $rules->argumentListDefinitionRenderingRules->spacesAfterArgumentComma = 1;
        $rules->argumentListDefinitionRenderingRules->addTrailingCommaToLastItemInChopDown = false;
        $rules->argumentListDefinitionRenderingRules->padTypeNames = false;
        $rules->argumentListDefinitionRenderingRules->padIdentifiers = false;
        $rules->functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules();
        $rules->functionDefinitionRenderingRules->argumentsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->functionDefinitionRenderingRules->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functionDefinitionRenderingRules->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->functionDefinitionRenderingRules->spacesBetweenReturnColonAndType = 1;
        $rules->functionDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

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