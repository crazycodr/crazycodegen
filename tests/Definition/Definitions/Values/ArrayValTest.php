<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArrayValTest extends TestCase
{
    use TokenFunctions;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNoKeysArePrintedBecauseTheyAreSequential()
    {
        $token = new ArrayVal([1, 2, 3]);

        $this->assertEquals(
            <<<'EOS'
            [1,2,3]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testIntKeysNotInSequentialOrderGetsAddedToArray()
    {
        $token = new ArrayVal([0 => 1, 2 => 2, 3 => 3]);

        $this->assertEquals(
            <<<'EOS'
            [0=>1,2=>2,3=>3]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testStringKeysGetsAllKeysAddedToArray()
    {
        $token = new ArrayVal(['0' => 1, 2 => 2, 'hello' => 3]);

        $this->assertEquals(
            <<<'EOS'
            [0=>1,2=>2,'hello'=>3]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNumericalIntKeysAreTransformedToIntKeysBecauseOfPhp()
    {
        $token = new ArrayVal(['0' => 1, '2' => 2, 'hello' => 3]);

        $this->assertEquals(
            <<<'EOS'
            [0=>1,2=>2,'hello'=>3]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testWrappingIsDoneWhenLineIsTooLong()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testStringValuesAreProperlyConverted()
    {
        $token = new ArrayVal([
            'this' => 'is a string',
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>'is a string']
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testBoolValuesAreProperlyConverted()
    {
        $token = new ArrayVal([
            'this' => true,
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>true]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNullValuesAreProperlyConverted()
    {
        $token = new ArrayVal([
            'this' => null,
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>null]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testTokenGroupValuesAreRenderedIn()
    {
        $token = new ArrayVal([
            'this' => new Expression('1+2'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>1+2]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testTokenValuesAreSimplyReused()
    {
        $token = new ArrayVal([
            'this' => new Expression('$someDirectIdentifier'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            ['this'=>$someDirectIdentifier]
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNestedTokenGroupsAreProperlyIndented()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
