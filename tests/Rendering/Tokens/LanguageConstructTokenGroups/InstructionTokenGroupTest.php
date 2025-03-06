<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\InstructionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class InstructionTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testSingleTokenIsRenderedWithTrailingSemiColon()
    {
        $token = new InstructionTokenGroup(
            new Token(1),
        );

        $this->assertEquals(<<<EOS
            1;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTokenGroupIsRenderedWithTrailingSemiColon()
    {
        $token = new InstructionTokenGroup(
            new SingleTypeTokenGroup('int'),
        );

        $this->assertEquals(<<<EOS
            int;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testArrayOfTokensIsReturnedAsIsWithTrailingSemiColon()
    {
        $token = new InstructionTokenGroup(
            [new Token(1), new CommaToken(), new Token(2)],
        );

        $this->assertEquals(<<<EOS
            1,2;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRenderedWithTrailingSemiColon()
    {
        $token = new InstructionTokenGroup(
            [new Token(1), new SpacesToken(), new SingleTypeTokenGroup('string')],
        );

        $this->assertEquals(<<<EOS
            1 string;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}