<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AsteriskToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ConditionTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testSpacesAreAddedAfterIf()
    {
        $token = new Condition(
            condition: new Token('true'),
            trueInstructions: new Token('true'),
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
            ],
            trueInstructions: new Token('true'),
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
            condition: new Token('true'),
            trueInstructions: new Token('true'),
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
            condition: new Token('true'),
            trueInstructions: new Token('true'),
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
            condition: new Token('true'),
            trueInstructions: [
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
            condition: new Token('true'),
            trueInstructions: new Token('true'),
            falseInstructions: new Token('false'),
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
            condition: new Token('true'),
            trueInstructions: new Token('true'),
            falseInstructions: new Token('false'),
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
        $token = new Condition(
            condition: new Token('true'),
            trueInstructions: new Token('0'),
            falseInstructions: new Condition(
                condition: [new Token(1), new Token('==='), new Token(2)],
                trueInstructions: new Token('1'),
                falseInstructions: new Condition(
                    condition: [new Token(2), new Token('==='), new Token(3)],
                    trueInstructions: new Token('2'),
                    falseInstructions: new Condition(
                        condition: [new Token(3), new Token('==='), new Token(4)],
                        trueInstructions: new Token('3'),
                        falseInstructions: new Token('4'),
                    ),
                ),
            ),
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
