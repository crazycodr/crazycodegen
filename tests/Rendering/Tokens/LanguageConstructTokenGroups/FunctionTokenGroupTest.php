<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\DocBlockTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\FunctionTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FunctionTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testInlineDefinitionRendersFunctionKeyword()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    private function getTestRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->docBlocks->lineLength = 80;
        $rules->argumentLists->addSeparatorToLastItem = true;
        $rules->argumentLists->padIdentifiers = false;
        $rules->argumentLists->padTypes = false;
        $rules->argumentLists->spacesAfterSeparator = 1;
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $rules->functions->closingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->openingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->newLinesAfterDocBlock = 1;
        $rules->functions->spacesBeforeOpeningBrace = 1;
        $rules->functions->spacesAfterArguments = 0;
        $rules->functions->spacesAfterIdentifier = 0;
        $rules->functions->spacesAfterReturnColon = 1;
        return $rules;
    }

    public function testInlineDefinitionRendersNameOfFunction()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterIdentifier = 1;

        $this->assertEquals(<<<EOS
            function myFunction ()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersArgumentListInlineAsExpectedBetweenParentheses()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListTokenGroup(
                arguments: [
                    new ArgumentTokenGroup(name: 'foo'),
                    new ArgumentTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            function myFunction(\$foo, int \$bar, bool \$baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterArguments = 1;

        $this->assertEquals(<<<EOS
            function myFunction() : string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterReturnColon = 2;

        $this->assertEquals(<<<EOS
            function myFunction():  string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(<<<EOS
            function myFunction(): string  {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->closingBrace = BracePositionEnum::NEXT_LINE;

        $this->assertEquals(<<<EOS
            function myFunction()  {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->closingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(<<<EOS
            function myFunction() {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnSameLineAndClosingBraceOnNextLineAsPerConfiguration()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->closingBrace = BracePositionEnum::NEXT_LINE;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->closingBrace = BracePositionEnum::SAME_LINE;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->closingBrace = BracePositionEnum::NEXT_LINE;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterIdentifier = 1;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListTokenGroup(
                arguments: [
                    new ArgumentTokenGroup(name: 'foo'),
                    new ArgumentTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            function myFunction(
                \$foo,
                int \$bar,
                bool \$baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterArguments = 1;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterReturnColon = 2;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::NEXT_LINE;
        $rules->functions->closingBrace = BracePositionEnum::NEXT_LINE;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListTokenGroup(
                arguments: [
                    new ArgumentTokenGroup(name: 'foo'),
                    new ArgumentTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->lineLength = 40;
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::NEVER;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListTokenGroup(
                arguments: [
                    new ArgumentTokenGroup(name: 'foo'),
                    new ArgumentTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;

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
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListTokenGroup(
                arguments: [
                    new ArgumentTokenGroup(name: 'foo'),
                    new ArgumentTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->lineLength = 40;
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;

        $this->assertEquals(<<<EOS
            function myFunction(
                \$foo,
                int \$bar,
                bool \$baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionEvenIfArgumentsWouldFitButConfigurationForcesIt()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            arguments: new ArgumentListTokenGroup(
                arguments: [
                    new ArgumentTokenGroup(name: 'foo'),
                    new ArgumentTokenGroup(name: 'bar', type: 'int'),
                    new ArgumentTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(<<<EOS
            function myFunction(
                \$foo,
                int \$bar,
                bool \$baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDocBlockIsProperlyRendered()
    {
        $token = new FunctionTokenGroup(
            name: 'myFunction',
            docBlock: new DocBlockTokenGroup(['This is a docblock that should be wrapped and displayed before the function declaration.']),
        );

        $rules = $this->getTestRules();
        $rules->docBlocks->lineLength = 40;
        $rules->functions->newLinesAfterDocBlock = 3;

        $this->assertEquals(<<<EOS
            /**
             * This is a docblock that should be
             * wrapped and displayed before the
             * function declaration.
             */
            
            
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}