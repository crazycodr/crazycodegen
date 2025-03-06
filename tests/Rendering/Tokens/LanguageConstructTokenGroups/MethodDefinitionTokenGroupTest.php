<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MethodDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;
use PHPUnit\Framework\TestCase;

class MethodDefinitionTokenGroupTest extends TestCase
{
    use RenderTokensToStringTrait;

    public function testDeclarationRendersAbstractKeywordWithSpaces()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            abstract: true,
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenAbstractAndNextToken = 4;

        $this->assertEquals(<<<EOS
            abstract    public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testDeclarationRendersPublicVisibilityByDefault()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testDeclarationRendersStaticKeywordAndSpaces()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            static: true,
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenStaticAndNextToken = 4;

        $this->assertEquals(<<<EOS
            public static    function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testDeclarationRendersVisibilityWithSpaces()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            visibility: VisibilityEnum::PROTECTED,
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenVisibilityAndNextToken = 4;

        $this->assertEquals(<<<EOS
            protected    function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersFunctionKeyword()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNameOfFunction()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenIdentifierAndArgumentList = 1;

        $this->assertEquals(<<<EOS
            public function myFunction ()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersArgumentListInlineAsExpectedBetweenParentheses()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction(\$foo, int \$bar, bool \$baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenArgumentListAndReturnColon = 1;

        $this->assertEquals(<<<EOS
            public function myFunction() : string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenReturnColonAndType = 2;

        $this->assertEquals(<<<EOS
            public function myFunction():  string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(<<<EOS
            public function myFunction(): string  {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(<<<EOS
            public function myFunction()  {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(<<<EOS
            public function myFunction() {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnSameLineAndClosingBraceOnNextLineAsPerConfiguration()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->funcOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::NEXT_LINE;

        $this->assertEquals(
            <<<EOS
            public function myFunction() {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnNextLineWithClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<EOS
            public function myFunction()
            {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBracesOnSeparateLinesAsPerConfiguration()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::NEXT_LINE;

        $this->assertEquals(
            <<<EOS
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersFunctionKeyword()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNameOfFunction()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesBetweenIdentifierAndArgumentList = 1;

        $this->assertEquals(<<<EOS
            public function myFunction (
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersArgumentListChopDownAsExpectedBetweenParentheses()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(<<<EOS
            public function myFunction(
                     \$foo,
                int  \$bar,
                bool \$baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesBetweenArgumentListAndReturnColon = 1;

        $this->assertEquals(<<<EOS
            public function myFunction(
            ) : string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesBetweenReturnColonAndType = 2;

        $this->assertEquals(<<<EOS
            public function myFunction(
            ):  string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(<<<EOS
            public function myFunction(
            ): string  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenThemAsPerRules()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesBeforeOpeningBraceIfSameLine = 2;

        $this->assertEquals(<<<EOS
            public function myFunction(
            )  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningAndClosingBracesPositionIsNotRespectedAndAlwaysSameLineNextLine()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::NEXT_LINE;

        $this->assertEquals(<<<EOS
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIsNeverWrap()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::NEVER;

        $this->assertEquals(<<<EOS
            public function myFunction(\$foo, int \$bar, bool \$baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIfTooLongButItStillFits()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            public function myFunction(\$foo, int \$bar, bool \$baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionIfArgumentsOnDifferentLinesChopIfTooLongAndItDoesNotFit()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 30;

        $this->assertEquals(<<<EOS
            public function myFunction(
                     \$foo,
                int  \$bar,
                bool \$baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionEvenIfArgumentsWouldFitButConfigurationForcesIt()
    {
        $token = new MethodDefinitionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListDeclarationTokenGroup(
                arguments: [
                    new ArgumentDeclarationTokenGroup(name: 'foo'),
                    new ArgumentDeclarationTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentDeclarationTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(<<<EOS
            public function myFunction(
                     \$foo,
                int  \$bar,
                bool \$baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    private function getBaseTestingRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->argumentLists->spacesAfterArgumentComma = 1;
        $rules->argumentLists->addTrailingCommaToLastItemInChopDown = true;
        $rules->argumentLists->padTypeNames = true;
        $rules->argumentLists->padIdentifiers = true;
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $rules->methods->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->methods->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->methods->spacesBetweenAbstractAndNextToken = 1;
        $rules->methods->spacesBetweenVisibilityAndNextToken = 1;
        $rules->methods->spacesBetweenStaticAndNextToken = 1;
        $rules->methods->spacesBetweenFunctionAndIdentifier = 1;
        $rules->methods->spacesBetweenIdentifierAndArgumentList = 0;
        $rules->methods->spacesBetweenArgumentListAndReturnColon = 0;
        $rules->methods->spacesBetweenReturnColonAndType = 1;
        $rules->methods->spacesBeforeOpeningBraceIfSameLine = 1;
        return $rules;
    }
}