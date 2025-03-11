<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ExpressionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ExpressionTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testSingleTokenIsRenderedAsExpected()
    {
        $token = new ExpressionTokenGroup(
            new Token(1),
        );

        $this->assertEquals(
            <<<'EOS'
            1
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTokenGroupIsRenderedAsExpected()
    {
        $token = new ExpressionTokenGroup(
            new SingleTypeDefinition('int'),
        );

        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testArrayOfTokensIsReturnedAsIs()
    {
        $token = new ExpressionTokenGroup(
            [new Token(1), new CommaToken(), new Token(2)],
        );

        $this->assertEquals(
            <<<'EOS'
            1,2
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRendered()
    {
        $token = new ExpressionTokenGroup(
            [new Token(1), new SpacesToken(), new SingleTypeDefinition('string')],
        );

        $this->assertEquals(
            <<<'EOS'
            1 string
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}
