<?php

namespace CrazyCodeGen\Tests\Definition\Values;

use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PlusToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArrayValTest extends TestCase
{
    use TokenFunctions;

    public function testLongFormUsedWhenConfigurationSaysSoAndNoKeysArePrintedBecauseTheyAreSequential()
    {
        $token = new ArrayVal([1, 2, 3]);

        $rules = $this->getRenderingRules();
        $rules->arrays->useShortForm = false;

        $this->assertEquals(
            <<<'EOS'
            array(1, 2, 3)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testShortFormUsedWhenConfigurationSaysSoAndNoKeysArePrintedBecauseTheyAreSequential()
    {
        $token = new ArrayVal([1, 2, 3]);

        $rules = $this->getRenderingRules();

        $this->assertEquals(
            <<<'EOS'
            [1, 2, 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testIntKeysNotInSequentialOrderGetsAddedToArray()
    {
        $token = new ArrayVal([0 => 1, 2 => 2, 3 => 3]);

        $rules = $this->getRenderingRules();

        $this->assertEquals(
            <<<'EOS'
            [0 => 1, 2 => 2, 3 => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testStringKeysGetsAllKeysAddedToArray()
    {
        $token = new ArrayVal(['0' => 1, 2 => 2, 'hello' => 3]);

        $rules = $this->getRenderingRules();

        $this->assertEquals(
            <<<'EOS'
            [0 => 1, 2 => 2, 'hello' => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testNumericalIntKeysAreTransformedToIntKeysBecauseOfPhp()
    {
        $token = new ArrayVal(['0' => 1, '2' => 2, 'hello' => 3]);

        $rules = $this->getRenderingRules();

        $this->assertEquals(
            <<<'EOS'
            [0 => 1, 2 => 2, 'hello' => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testSpacesAfterIdentifiersAreRespected()
    {
        $token = new ArrayVal(['hello' => 1, 'world' => 2, 'foo' => 3]);

        $rules = $this->getRenderingRules();
        $rules->arrays->spacesAfterIdentifiers = 3;

        $this->assertEquals(
            <<<'EOS'
            ['hello'   => 1, 'world'   => 2, 'foo'   => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testSpacesAfterOperatorsAreRespected()
    {
        $token = new ArrayVal(['hello' => 1, 'world' => 2, 'foo' => 3]);

        $rules = $this->getRenderingRules();
        $rules->arrays->spacesAfterOperators = 3;

        $this->assertEquals(
            <<<'EOS'
            ['hello' =>   1, 'world' =>   2, 'foo' =>   3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testSpacesAfterValuesAreRespected()
    {
        $token = new ArrayVal(['hello' => 1, 'world' => 2, 'foo' => 3]);

        $rules = $this->getRenderingRules();
        $rules->arrays->spacesAfterValues = 3;

        $this->assertEquals(
            <<<'EOS'
            ['hello' => 1   , 'world' => 2   , 'foo' => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testSpacesAfterSeparatorsAreRespected()
    {
        $token = new ArrayVal(['hello' => 1, 'world' => 2, 'foo' => 3]);

        $rules = $this->getRenderingRules();
        $rules->arrays->spacesAfterSeparators = 3;

        $this->assertEquals(
            <<<'EOS'
            ['hello' => 1,   'world' => 2,   'foo' => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testWrappingIsDoneWhenLineIsTooLong()
    {
        $token = new ArrayVal([
            'thisIsAPrettyLongKey' => 1,
            'thisAlsoContributesToWrapping' => 2,
            'shortButWraps' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->lineLength = 80;
        $rules->arrays->wrap = WrappingDecision::IF_TOO_LONG;

        $this->assertEquals(
            <<<'EOS'
            [
                'thisIsAPrettyLongKey' => 1,
                'thisAlsoContributesToWrapping' => 2,
                'shortButWraps' => 3,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testWrappingIsDoneEvenIfLineNotTooLong()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1,
                'is' => 2,
                'short' => 3,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testWrappingIsNotDoneEvenIfLineTooLong()
    {
        $token = new ArrayVal([
            'thisIsAPrettyLongKey' => 1,
            'thisAlsoContributesToWrapping' => 2,
            'shortButWraps' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->lineLength = 100;
        $rules->arrays->wrap = WrappingDecision::NEVER;

        $this->assertEquals(
            <<<'EOS'
            ['thisIsAPrettyLongKey' => 1, 'thisAlsoContributesToWrapping' => 2, 'shortButWraps' => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testOpeningBraceOnSameLineIfWrappingIndentsAllOtherItemsButFirst()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;
        $rules->arrays->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->arrays->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            ['this' => 1,
                'is' => 2,
                'short' => 3,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testOpeningBraceOnDiffLineIfWrappingIsPropertyIndented()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;
        $rules->arrays->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->arrays->closingBrace = BracePositionEnum::DIFF_LINE;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1,
                'is' => 2,
                'short' => 3,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testClosingBraceOnSameLineIfWrappingHidesLastItemComma()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;
        $rules->arrays->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->arrays->closingBrace = BracePositionEnum::SAME_LINE;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1,
                'is' => 2,
                'short' => 3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testClosingBraceOnDiffLineIfWrappingShowsLastItemCommaIfConfigured()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;
        $rules->arrays->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->arrays->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->arrays->addSeparatorToLastItem = true;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1,
                'is' => 2,
                'short' => 3,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testWrappedConfigDoesNotFeatureSpacesAfterSeparatorAndHidesLastSeparatorWhenConfigured()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;
        $rules->arrays->addSeparatorToLastItem = false;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1,
                'is' => 2,
                'short' => 3
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testPaddingOfIdentifiersIsAppliedIfConfigured()
    {
        $token = new ArrayVal([
            'this' => 1,
            'is' => 2,
            'short' => 3
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1,
                'is' => 2,
                'short' => 3,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testStringValuesAreProperlyConverted()
    {
        $token = new ArrayVal([
            'this' => 'is a string',
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 'is a string',
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testBoolValuesAreProperlyConverted()
    {
        $token = new ArrayVal([
            'this' => true,
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => true,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testNullValuesAreProperlyConverted()
    {
        $token = new ArrayVal([
            'this' => null,
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => null,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testTokenGroupValuesAreRenderedIn()
    {
        $token = new ArrayVal([
            'this' => new Expression([new Token(1), new PlusToken(), new Token(2)]),
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => 1+2,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testTokenValuesAreSimplyReused()
    {
        $token = new ArrayVal([
            'this' => new Token('$someDirectIdentifier'),
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'this' => $someDirectIdentifier,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testNestedTokenGroupsAreProperlyIndented()
    {
        $token = new ArrayVal([
            'hello' => new ArrayVal([
                'foo' => 'bar',
                'bar' => 'baz',
            ]),
            'world' => 123,
        ]);

        $rules = $this->getRenderingRules();
        $rules->arrays->wrap = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<'EOS'
            [
                'hello' => [
                    'foo' => 'bar',
                    'bar' => 'baz',
                ],
                'world' => 123,
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function getRenderingRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->arrays->useShortForm = true;
        $rules->arrays->wrap = WrappingDecision::IF_TOO_LONG;
        $rules->arrays->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->arrays->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->arrays->padIdentifiers = false;
        $rules->arrays->spacesAfterIdentifiers = 1;
        $rules->arrays->spacesAfterOperators = 1;
        $rules->arrays->spacesAfterValues = 0;
        $rules->arrays->spacesAfterSeparators = 1;
        $rules->arrays->addSeparatorToLastItem = true;
        return $rules;
    }
}
