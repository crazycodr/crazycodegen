<?php

namespace CrazyCodeGen\Tests\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class InstructionTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testSingleTokenIsRenderedWithTrailingSemiColon(): void
    {
        $token = new Instruction([
            new Expression('1'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            1;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTokenGroupIsRenderedWithTrailingSemiColon(): void
    {
        $token = new Instruction([
            new BuiltInTypeSpec(BuiltInTypesEnum::int),
        ]);

        $this->assertEquals(
            <<<'EOS'
            int;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testArrayOfTokensIsReturnedAsIsWithTrailingSemiColon(): void
    {
        $token = new Instruction([
            new Expression('1,2'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            1,2;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testMixOfTokensAndTokenGroupsIsRenderedWithTrailingSemiColon(): void
    {
        $token = new Instruction([
            new Expression('1 string'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            1 string;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
