<?php

namespace CrazyCodeGen\Tests\Expressions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testComplexConditionWithDifferentTokensAndTokenGroupsIsRenderedAsIsAndInsideParentheses(): void
    {
        $token = new Condition(
            condition: new Expression('1 === (1*3)'),
            instructions: [
                new Expression('true'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            if(1 === (1*3)){true;}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testComplexTrueInstructionsAreRenderedInBody(): void
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

        $this->assertEquals(
            <<<'EOS'
            if(true){1 === (1*3);return 1;}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
