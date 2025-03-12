<?php

namespace CrazyCodeGen\Tests\Expressions\Structures;

use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AsteriskToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    use TokenFunctions;

    public function testSpacesAreAddedAfterIf()
    {
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Expression('true'),
            ],
        );

        $rules = $this->getTestRules();
        $rules->conditions->spacesAfterKeyword = 4;

        $this->assertEquals(
            <<<'EOS'
            if    (true) {
                true;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    private function getTestRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->conditions->spacesAfterKeyword = 1;
        $rules->conditions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->conditions->keywordAfterClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->conditions->spacesBeforeOpeningBrace = 1;
        $rules->conditions->spacesAfterClosingBrace = 1;
        return $rules;
    }

    public function testComplexConditionWithDifferentTokensAndTokenGroupsIsRenderedAsIsAndInsideParentheses()
    {
        $token = new Condition(
            condition: [
                new Expression(1),
                new SpacesToken(),
                new Expression('==='),
                new SpacesToken(),
                [
                    new ParStartToken(),
                    new Expression(1),
                    new AsteriskToken(),
                    new Expression(3),
                    new ParEndToken(),
                ],
            ],
            trueInstructions: [
                new Expression('true'),
            ],
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if (1 === (1*3)) {
                true;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testOpenBraceIsOnSameLineWithExpectedSpaces()
    {
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Expression('true'),
            ],
        );

        $rules = $this->getTestRules();
        $rules->conditions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->conditions->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<'EOS'
            if (true)    {
                true;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testOpenBraceIsOnDiffLineAndSpacesRuleDisregarded()
    {
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Expression('true'),
            ],
        );

        $rules = $this->getTestRules();
        $rules->conditions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->conditions->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<'EOS'
            if (true)
            {
                true;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testComplexTrueInstructionsAreRenderedInBodyIndented()
    {
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Instruction(
                    instructions: [
                        new Expression('1 === (1*3)'),
                    ]
                ),
                new Instruction(
                    instructions: [
                        new ReturnOp(1),
                    ]
                ),
            ],
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if (true) {
                1 === (1*3);
                return 1;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testClosingBraceIsOnSameLineAsElseWhenFalseConditionsExist()
    {
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Expression('true'),
            ],
            falseInstructions: [
                new Expression('false'),
            ],
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if (true) {
                true;
            } else {
                false;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testBracesOnDifferentLinesDoNotAddSpaces()
    {
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Expression('true'),
            ],
            falseInstructions: [
                new Expression('false'),
            ],
        );

        $rules = $this->getTestRules();
        $rules->conditions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->conditions->keywordAfterClosingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            if (true)
            {
                true;
            }
            else
            {
                false;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testConditionTokenGroupAsFalseInstructionsActAsElseIfAndCascadesWithUnlimitedElseIfs()
    {
        $this->markTestSkipped('Will change');
        $token = new Condition(
            condition: new Expression('true'),
            trueInstructions: [
                new Expression('0'),
            ],
            falseInstructions: [
                new Condition(
                    condition: [new Expression(1), new Expression('==='), new Expression(2)],
                    trueInstructions: [
                        new Expression('1'),
                    ],
                    falseInstructions: [
                        new Condition(
                            condition: [new Expression(2), new Expression('==='), new Expression(3)],
                            trueInstructions: [
                                new Expression('2'),
                            ],
                            falseInstructions: [
                                new Condition(
                                    condition: [new Expression(3), new Expression('==='), new Expression(4)],
                                    trueInstructions: [
                                        new Expression('3'),
                                    ],
                                    falseInstructions: [
                                        new Expression('4'),
                                    ],
                                ),
                            ],
                        ),
                    ],
                ),
            ],
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if (true) {
                0;
            } elseif (1===2) {
                1;
            } elseif (2===3) {
                2;
            } elseif (3===4) {
                3;
            } else {
                4;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }
}
