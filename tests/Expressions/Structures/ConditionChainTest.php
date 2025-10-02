<?php

namespace CrazyCodeGen\Tests\Expressions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Definition\Expressions\Structures\ConditionChain;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ConditionChainTest extends TestCase
{
    use TokenFunctions;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testSingleConditionIsRenderedAsExpected()
    {
        $token = new ConditionChain([
            new Condition(
                condition: new Expression('$foo === "bar"'),
                instructions: [
                    new Expression('true'),
                ]
            ),
        ]);

        $this->assertEquals(
            <<<'EOS'
            if($foo === "bar"){true;}
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testSecondConditionWithoutConditionActuallyGeneratesAnElseCase()
    {
        $token = new ConditionChain([
            new Condition(
                condition: new Expression('$foo === "bar"'),
                instructions: [
                    new Expression('true'),
                ]
            ),
            new Condition(
                instructions: [
                    new Expression('false'),
                ]
            ),
        ]);

        $this->assertEquals(
            <<<'EOS'
            if($foo === "bar"){true;}else{false;}
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testSecondConditionWithConditionGeneratesAnElseIfCase()
    {
        $token = new ConditionChain([
            new Condition(
                condition: new Expression('$foo === "bar"'),
                instructions: [
                    new Expression('true'),
                ]
            ),
            new Condition(
                condition: new Expression('$bar === "baz"'),
                instructions: [
                    new Expression('false'),
                ]
            ),
        ]);

        $this->assertEquals(
            <<<'EOS'
            if($foo === "bar"){true;}elseif($bar === "baz"){false;}
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testMultipleConditionsGeneratesChainOfConditions()
    {
        $token = new ConditionChain([
            new Condition(
                condition: new Expression('$foo === "bar"'),
                instructions: [
                    new Expression('true'),
                ]
            ),
            new Condition(
                condition: new Expression('$bar === "baz"'),
                instructions: [
                    new Expression('false'),
                ]
            ),
            new Condition(
                instructions: [
                    new Expression('"foo"'),
                ]
            ),
        ]);

        $this->assertEquals(
            <<<'EOS'
            if($foo === "bar"){true;}elseif($bar === "baz"){false;}else{"foo";}
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testBracePositionRuleChangesProvidesExpectedStructure()
    {
        $token = new ConditionChain([
            new Condition(
                condition: new Expression('$foo === "bar"'),
                instructions: [
                    new Expression('true'),
                ]
            ),
            new Condition(
                condition: new Expression('$bar === "baz"'),
                instructions: [
                    new Expression('false'),
                ]
            ),
            new Condition(
                instructions: [
                    new Expression('"foo"'),
                ]
            ),
        ]);

        $this->assertEquals(
            <<<'EOS'
            if($foo === "bar"){true;}elseif($bar === "baz"){false;}else{"foo";}
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
