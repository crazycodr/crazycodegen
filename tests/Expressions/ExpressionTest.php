<?php

namespace CrazyCodeGen\Tests\Expressions;

use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    use TokenFunctions;

    public function testSingleTokenIsRenderedAsExpected()
    {
        $token = new Expression(
            new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            1
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTokenGroupIsRenderedAsExpected()
    {
        $token = new Expression(
            new BuiltInTypeSpec('int'),
        );

        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testArrayOfTokensIsReturnedAsIs()
    {
        $token = new Expression(
            [new Token(1), new CommaToken(), new Token(2)],
        );

        $this->assertEquals(
            <<<'EOS'
            1,2
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRendered()
    {
        $token = new Expression(
            [new Token(1), new SpacesToken(), new BuiltInTypeSpec('string')],
        );

        $this->assertEquals(
            <<<'EOS'
            1 string
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
