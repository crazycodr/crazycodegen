<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\MultiTypeDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
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
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
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
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testStringTypesAreConvertedToSingleTypeTokenGroupsAndRendered()
    {
        $token = new MultiTypeDef(['string', 'int']);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testParenthesesAreAddedAroundTokensWhenNestedIsTurnedOn()
    {
        $token = new MultiTypeDef(['string', 'int'], nestedTypes: true);

        $this->assertEquals(
            <<<'EOS'
            (string|int)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
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
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
