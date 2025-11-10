<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArrayValTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNoKeysArePrintedBecauseTheyAreSequential(): void
    {
        $token = new ArrayVal([1, 2, 3]);

        $this->assertEquals(
            <<<'EOS'
            [1,2,3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testIntKeysNotInSequentialOrderGetsAddedToArray(): void
    {
        $token = new ArrayVal([0 => 1, 2 => 2, 3 => 3]);

        $this->assertEquals(
            <<<'EOS'
            [0=>1,2=>2,3=>3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testStringKeysGetsAllKeysAddedToArray(): void
    {
        $token = new ArrayVal(['0' => 1, 2 => 2, 'hello' => 3]);

        $this->assertEquals(
            <<<'EOS'
            [0=>1,2=>2,'hello'=>3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNumericalIntKeysAreTransformedToIntKeysBecauseOfPhp(): void
    {
        $token = new ArrayVal(['0' => 1, '2' => 2, 'hello' => 3]);

        $this->assertEquals(
            <<<'EOS'
            [0=>1,2=>2,'hello'=>3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testWrappingIsDoneWhenLineIsTooLong(): void
    {
        $token = new ArrayVal([
            'thisIsAPrettyLongKey' => 1,
            'thisAlsoContributesToWrapping' => 2,
            'shortButWraps' => 3
        ]);

        $this->assertEquals(
            <<<'EOS'
            [
            'thisIsAPrettyLongKey'=>1,
            'thisAlsoContributesToWrapping'=>2,
            'shortButWraps'=>3
            ]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext(maximumSingleLineArrayLength: 20)))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testWrappingIsOnlyDoneWhenRenderingContextIsSetToWrapEvenIfTooLong(): void
    {
        $token = new ArrayVal([
            'thisIsAPrettyLongKey' => 1,
            'thisAlsoContributesToWrapping' => 2,
            'shortButWraps' => 3
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['thisIsAPrettyLongKey'=>1,'thisAlsoContributesToWrapping'=>2,'shortButWraps'=>3]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testStringValuesAreProperlyConverted(): void
    {
        $token = new ArrayVal([
            'this' => 'is a string',
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>'is a string']
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testBoolValuesAreProperlyConverted(): void
    {
        $token = new ArrayVal([
            'this' => true,
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>true]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNullValuesAreProperlyConverted(): void
    {
        $token = new ArrayVal([
            'this' => null,
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>null]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testTokenGroupValuesAreRenderedIn(): void
    {
        $token = new ArrayVal([
            'this' => new Expression('1+2'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>1+2]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testTokenValuesAreSimplyReused(): void
    {
        $token = new ArrayVal([
            'this' => new Expression('$someDirectIdentifier'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>$someDirectIdentifier]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNestedTokenGroupsAreProperlyIndented(): void
    {
        $token = new ArrayVal([
            'hello' => new ArrayVal([
                'foo' => 'bar',
                'bar' => 'baz',
            ]),
            'world' => 123,
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['hello'=>['foo'=>'bar','bar'=>'baz'],'world'=>123]
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
