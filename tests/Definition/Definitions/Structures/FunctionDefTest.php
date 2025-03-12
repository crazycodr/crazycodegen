<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\FunctionDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FunctionDefTest extends TestCase
{
    use TokenFunctions;

    public function testInlineDefinitionRendersFunctionKeyword()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
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
        $rules->parameterLists->addSeparatorToLastItem = true;
        $rules->parameterLists->padIdentifiers = false;
        $rules->parameterLists->padTypes = false;
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $rules->functions->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->functions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->functions->newLinesAfterDocBlock = 1;
        $rules->functions->spacesBeforeOpeningBrace = 1;
        $rules->functions->spacesAfterArguments = 0;
        $rules->functions->spacesAfterIdentifier = 0;
        $rules->functions->spacesAfterReturnColon = 1;
        return $rules;
    }

    public function testInlineDefinitionRendersNameOfFunction()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterIdentifier = 1;

        $this->assertEquals(
            <<<'EOS'
            function myFunction ()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersArgumentListInlineAsExpectedBetweenParentheses()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction($foo, int $bar, bool $baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterArguments = 1;

        $this->assertEquals(
            <<<'EOS'
            function myFunction() : string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterReturnColon = 2;

        $this->assertEquals(
            <<<'EOS'
            function myFunction():  string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(): string  {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction()  {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->closingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction() {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnSameLineAndClosingBraceOnDiffLineAsPerConfiguration()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->functions->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction() {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnDiffLineWithClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->functions->closingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction()
            {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBracesOnSeparateLinesAsPerConfiguration()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->functions->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersFunctionKeyword()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNameOfFunction()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterIdentifier = 1;

        $this->assertEquals(
            <<<'EOS'
            function myFunction (
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersArgumentListChopDownAsExpectedBetweenParentheses()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
                $foo,
                int $bar,
                bool $baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterArguments = 1;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ) : string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesAfterReturnColon = 2;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ):  string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ): string  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->spacesBeforeOpeningBrace = 2;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            )  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningAndClosingBracesPositionIsNotRespectedAndAlwaysSameLineDiffLine()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $rules = $this->getTestRules();
        $rules->functions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->functions->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIsNeverWrap()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->lineLength = 40;
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::NEVER;

        $this->assertEquals(
            <<<'EOS'
            function myFunction($foo, int $bar, bool $baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesChopIfTooLongButItStillFits()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;

        $this->assertEquals(
            <<<'EOS'
            function myFunction($foo, int $bar, bool $baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionIfArgumentsOnDifferentLinesChopIfTooLongAndItDoesNotFit()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->lineLength = 40;
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
                $foo,
                int $bar,
                bool $baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionEvenIfArgumentsWouldFitButConfigurationForcesIt()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getTestRules();
        $rules->functions->argumentsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            function myFunction(
                $foo,
                int $bar,
                bool $baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testDocBlockIsProperlyRendered()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the function declaration.']),
        );

        $rules = $this->getTestRules();
        $rules->docBlocks->lineLength = 40;
        $rules->functions->newLinesAfterDocBlock = 3;

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be
             * wrapped and displayed before the
             * function declaration.
             */
            
            
            function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testDocBlockDoesNotInterfereWithDecisionToChopDownArgumentList()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the function.']),
            arguments: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments1', type: 'int'),
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments2', type: 'int'),
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments3', type: 'int'),
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments4', type: 'int'),
                ],
            ),
            returnType: 'int',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be wrapped and displayed before the function.
             */
            function myFunction(
                int $longTokenThatWillContributeToWrappingArguments1,
                int $longTokenThatWillContributeToWrappingArguments2,
                int $longTokenThatWillContributeToWrappingArguments3,
                int $longTokenThatWillContributeToWrappingArguments4,
            ): int {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }
}
