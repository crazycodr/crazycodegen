<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AsteriskToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParameterListTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ParameterTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\DocBlockTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\InstructionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MethodTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MethodTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testDeclarationRendersAbstractKeywordWithSpaces()
    {
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
            name: 'myFunction',
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'foo'),
                    new ParameterTokenGroup(name: 'bar', type: 'int'),
                    new ParameterTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
            name: 'myFunction',
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'foo'),
                    new ParameterTokenGroup(name: 'bar', type: 'int'),
                    new ParameterTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
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
        $token = new MethodTokenGroup(
            name: 'myFunction',
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'foo'),
                    new ParameterTokenGroup(name: 'bar', type: 'int'),
                    new ParameterTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheInlineVersionIfArgumentsOnDifferentLinesIfTooLongButItStillFits()
    {
        $token = new MethodTokenGroup(
            name: 'myFunction',
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'foo'),
                    new ParameterTokenGroup(name: 'bar', type: 'int'),
                    new ParameterTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionIfArgumentsOnDifferentLinesChopIfTooLongAndItDoesNotFit()
    {
        $token = new MethodTokenGroup(
            name: 'myFunction',
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'foo'),
                    new ParameterTokenGroup(name: 'bar', type: 'int'),
                    new ParameterTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRenderReturnsTheChopDownVersionEvenIfArgumentsWouldFitButConfigurationForcesIt()
    {
        $token = new MethodTokenGroup(
            name: 'myFunction',
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'foo'),
                    new ParameterTokenGroup(name: 'bar', type: 'int'),
                    new ParameterTokenGroup(name: 'baz', type: 'bool', defaultValue: true),
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDocBlockIsProperlyRendered()
    {
        $token = new MethodTokenGroup(
            name: 'myFunction',
            docBlock: new DocBlockTokenGroup(['This is a docblock that should be wrapped and displayed before the function declaration.']),
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testAreRenderedInBodyIndented()
    {
        $token = new MethodTokenGroup(
            name: 'myFunction',
            instructions: [
                new InstructionTokenGroup(
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
                new InstructionTokenGroup(
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDocBlockDoesNotInterfereWithDecisionToChopDownArgumentList()
    {
        $token = new MethodTokenGroup(
            name: 'myFunction',
            docBlock: new DocBlockTokenGroup(['This is a docblock that should be wrapped and displayed before the function.']),
            parameters: new ParameterListTokenGroup(
                parameters: [
                    new ParameterTokenGroup(name: 'longTokenThatWillContributeToWrappingArguments1', type: 'int'),
                    new ParameterTokenGroup(name: 'longTokenThatWillContributeToWrappingArguments2', type: 'int'),
                    new ParameterTokenGroup(name: 'longTokenThatWillContributeToWrappingArguments3', type: 'int'),
                    new ParameterTokenGroup(name: 'longTokenThatWillContributeToWrappingArguments4', type: 'int'),
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
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}
