<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\MultiTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MultiTypeDefTest extends TestCase
{
    use TokenFunctions;

    public function testTypesAreJoinedWithPipeWhenUnionIsTrueByDefault()
    {
        $token = new MultiTypeDef([
            new BuiltInTypeSpec('string'),
            new BuiltInTypeSpec('int'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTypesAreJoinedWithAmpersandWhenUnionIsFalse()
    {
        $token = new MultiTypeDef(
            [
                new BuiltInTypeSpec('string'),
                new BuiltInTypeSpec('int'),
            ],
            unionTypes: false
        );

        $this->assertEquals(
            <<<'EOS'
            string&int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testStringTypesAreConvertedToSingleTypeTokenGroupsAndRendered()
    {
        $token = new MultiTypeDef(['string', 'int']);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testParenthesesAreAddedAroundTokensWhenNestedIsTurnedOn()
    {
        $token = new MultiTypeDef(['string', 'int'], nestedTypes: true);

        $this->assertEquals(
            <<<'EOS'
            (string|int)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testIfInnerTypeIsMultiTypeItGetsRenderedAtTheCorrectPlaceAndAllParenthesesAreRendered()
    {
        $token = new MultiTypeDef(
            [
                new MultiTypeDef(['int', 'float'], nestedTypes: true),
                new MultiTypeDef(['string', 'bool'], nestedTypes: true),
            ],
            unionTypes: false,
            nestedTypes: true,
        );

        $this->assertEquals(
            <<<'EOS'
            ((int|float)&(string|bool))
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
