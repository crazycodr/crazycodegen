<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDef;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AsteriskToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MethodDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testDeclarationRendersAbstractKeywordWithSpaces()
    {
        $token = new MethodDef(
            name: 'myFunction',
            abstract: true,
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterAbstract = 4;

        $this->assertEquals(
            <<<'EOS'
            abstract    public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    private function getBaseTestingRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->parameterLists->spacesAfterSeparator = 1;
        $rules->parameterLists->addSeparatorToLastItem = true;
        $rules->parameterLists->padTypes = true;
        $rules->parameterLists->padIdentifiers = true;
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $rules->methods->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->methods->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->methods->spacesAfterAbstract = 1;
        $rules->methods->spacesAfterVisibility = 1;
        $rules->methods->spacesAfterStatic = 1;
        $rules->methods->spacesAfterFunction = 1;
        $rules->methods->spacesAfterIdentifier = 0;
        $rules->methods->spacesAfterArguments = 0;
        $rules->methods->spacesAfterReturnColon = 1;
        $rules->methods->spacesBeforeOpeningBrace = 1;
        return $rules;
    }

    public function testDeclarationRendersPublicVisibilityByDefault()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testDeclarationRendersStaticKeywordAndSpaces()
    {
        $token = new MethodDef(
            name: 'myFunction',
            static: true,
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterStatic = 4;

        $this->assertEquals(
            <<<'EOS'
            public static    function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testDeclarationRendersVisibilityWithSpaces()
    {
        $token = new MethodDef(
            name: 'myFunction',
            visibility: VisibilityEnum::PROTECTED,
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterVisibility = 4;

        $this->assertEquals(
            <<<'EOS'
            protected    function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersFunctionKeyword()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNameOfFunction()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterIdentifier = 1;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction ()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersArgumentListInlineAsExpectedBetweenParentheses()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction($foo, int $bar, bool $baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterArguments = 1;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction() : string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterReturnColon = 2;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction():  string
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->closingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->spacesBeforeOpeningBrace = 2;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(): string  {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenListAndOpeningBraceAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->closingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->spacesBeforeOpeningBrace = 2;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()  {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->closingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction() {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnSameLineAndClosingBraceOnDiffLineAsPerConfiguration()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->methods->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction() {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningBraceOnDiffLineWithClosingBraceOnSameLineAsPerConfiguration()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->methods->closingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {}
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineDefinitionRendersOpeningAndClosingBracesOnSeparateLinesAsPerConfiguration()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->methods->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersFunctionKeyword()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNameOfFunction()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersNoSpaceBetweenNameAndArgumentList()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersSpacesBetweenNameAndArgumentListAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->spacesAfterIdentifier = 1;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction (
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersArgumentListChopDownAsExpectedBetweenParentheses()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
                     $foo,
                int  $bar,
                bool $baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenListAndReturnColonAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesAfterArguments = 1;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ) : string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenReturnColonAndTypeAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesAfterReturnColon = 2;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ):  string {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersReturnTypeAfterArgumentListWithSpacesBetweenTypeAndOpeningBraceAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesBeforeOpeningBrace = 2;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ): string  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningBraceAfterArgumentListWithSpacesBetweenThemAsPerRules()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->spacesBeforeOpeningBrace = 2;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            )  {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownDefinitionRendersOpeningAndClosingBracesPositionIsNotRespectedAndAlwaysSameLineDiffLine()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->methods->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->methods->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
            ) {
            }
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIsNeverWrap()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::NEVER;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction($foo, int $bar, bool $baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIfTooLongButItStillFits()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction($foo, int $bar, bool $baz = true)
            {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionIfArgumentsOnDifferentLinesChopIfTooLongAndItDoesNotFit()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 30;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
                     $foo,
                int  $bar,
                bool $baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionEvenIfArgumentsWouldFitButConfigurationForcesIt()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'foo'),
                    new ParameterDef(name: 'bar', type: 'int'),
                    new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
                ]
            )
        );

        $rules = $this->getBaseTestingRules();
        $rules->methods->argumentsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(
                     $foo,
                int  $bar,
                bool $baz = true,
            ) {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testDocBlockIsProperlyRendered()
    {
        $token = new MethodDef(
            name: 'myFunction',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the function declaration.']),
        );

        $rules = $this->getBaseTestingRules();
        $rules->docBlocks->lineLength = 40;
        $rules->methods->newLinesAfterDocBlock = 3;

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be
             * wrapped and displayed before the
             * function declaration.
             */
            
            
            public function myFunction()
            {
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testAreRenderedInBodyIndented()
    {
        $token = new MethodDef(
            name: 'myFunction',
            instructions: [
                new Instruction(
                    instructions: [
                        new Token(1),
                        new SpacesToken(),
                        new Token('==='),
                        new SpacesToken(),
                        [
                            new ParStartToken(),
                            new Token(1),
                            new AsteriskToken(),
                            new Token(3),
                            new ParEndToken(),
                        ],
                    ]
                ),
                new Instruction(
                    instructions: [
                        new Token('return'),
                        new SpacesToken(),
                        new Token(1),
                    ]
                ),
            ],
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            public function myFunction()
            {
                1 === (1*3);
                return 1;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testDocBlockDoesNotInterfereWithDecisionToChopDownArgumentList()
    {
        $token = new MethodDef(
            name: 'myFunction',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the function.']),
            parameters: new ParameterListDef(
                parameters: [
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments1', type: 'int'),
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments2', type: 'int'),
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments3', type: 'int'),
                    new ParameterDef(name: 'longTokenThatWillContributeToWrappingArguments4', type: 'int'),
                ],
            ),
            returnType: 'int',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be wrapped and displayed before the function.
             */
            public function myFunction(
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
