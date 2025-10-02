<?php

namespace CrazyCodeGen\Tests\Expressions;

use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    use TokenFunctions;

    public function testSingleTokenIsRenderedAsExpected()
    {
        $token = new Expression(1);

        $this->assertEquals(
            <<<'EOS'
            1
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTokenGroupIsRenderedAsExpected()
    {
        $token = new Expression('int');

        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testArrayOfValuesIsReturnedAsIs()
    {
        $token = new Expression('1,2');

        $this->assertEquals(
            <<<'EOS'
            1,2
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRendered()
    {
        $token = new Expression('1 string');

        $this->assertEquals(
            <<<'EOS'
            1 string
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
