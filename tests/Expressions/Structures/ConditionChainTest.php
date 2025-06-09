<?php

namespace CrazyCodeGen\Tests\Expressions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Definition\Expressions\Structures\ConditionChain;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
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

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if ($foo === "bar") {
                true;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
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

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if ($foo === "bar") {
                true;
            } else {
                false;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
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

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if ($foo === "bar") {
                true;
            } elseif ($bar === "baz") {
                false;
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
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

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            if ($foo === "bar") {
                true;
            } elseif ($bar === "baz") {
                false;
            } else {
                "foo";
            }
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
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

        $rules = $this->getTestRules();
        $rules->conditions->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->conditions->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            if ($foo === "bar")
            {
                true;
            }
            elseif ($bar === "baz")
            {
                false;
            }
            else
            {
                "foo";
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
