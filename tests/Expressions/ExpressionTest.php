<?php

namespace CrazyCodeGen\Tests\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testSingleTokenIsRenderedAsExpected(): void
    {
        $token = new Expression('1');

        $this->assertEquals(
            <<<'EOS'
            1
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTokenGroupIsRenderedAsExpected(): void
    {
        $token = new Expression('int');

        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testArrayOfValuesIsReturnedAsIs(): void
    {
        $token = new Expression('1,2');

        $this->assertEquals(
            <<<'EOS'
            1,2
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRendered(): void
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
