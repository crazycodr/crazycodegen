<?php

namespace CrazyCodeGen\Tests\Expressions\Structures;

use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    use TokenFunctions;

    public function testSpacesAreAddedAfterIf()
    {
        $token = new Condition(
            condition: new Expression('true'),
            instructions: [
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

    public function testComplexConditionWithDifferentTokensAndTokenGroupsIsRenderedAsIsAndInsideParentheses()
    {
        $token = new Condition(
            condition: new Expression('1 === (1*3)'),
            instructions: [
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
            instructions: [
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
            instructions: [
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
            instructions: [
                new Instruction(
                    expressions: [
                        new Expression('1 === (1*3)'),
                    ]
                ),
                new Instruction(
                    expressions: [
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

    private function getTestRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->conditions->spacesAfterKeyword = 1;
        $rules->conditions->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->conditions->closingBrace = BracePositionEnum::SAME_LINE;
        $rules->conditions->spacesBeforeOpeningBrace = 1;
        $rules->conditions->spacesAfterClosingBrace = 1;
        return $rules;
    }
}
