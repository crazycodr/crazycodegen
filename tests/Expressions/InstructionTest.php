<?php

namespace CrazyCodeGen\Tests\Expressions;

use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class InstructionTest extends TestCase
{
    use TokenFunctions;

    public function testSingleTokenIsRenderedWithTrailingSemiColon()
    {
        $token = new Instruction([
            new Expression(1),
        ]);

        $this->assertEquals(
            <<<'EOS'
            1;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTokenGroupIsRenderedWithTrailingSemiColon()
    {
        $token = new Instruction([
            new BuiltInTypeSpec('int'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            int;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testArrayOfTokensIsReturnedAsIsWithTrailingSemiColon()
    {
        $token = new Instruction([
            new Expression('1,2'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            1,2;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRenderedWithTrailingSemiColon()
    {
        $token = new Instruction([
            new Expression('1 string'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            1 string;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
