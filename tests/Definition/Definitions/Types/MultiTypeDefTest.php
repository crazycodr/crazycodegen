<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\MultiTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MultiTypeDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testTypesAreJoinedWithPipeWhenUnionIsTrueByDefault(): void
    {
        $token = new MultiTypeDef([
            new BuiltInTypeSpec(BuiltInTypesEnum::string),
            new BuiltInTypeSpec(BuiltInTypesEnum::int),
        ]);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTypesAreJoinedWithAmpersandWhenUnionIsFalse(): void
    {
        $token = new MultiTypeDef(
            [
                new BuiltInTypeSpec(BuiltInTypesEnum::string),
                new BuiltInTypeSpec(BuiltInTypesEnum::int),
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

    public function testStringTypesAreConvertedToSingleTypeTokenGroupsAndRendered(): void
    {
        $token = new MultiTypeDef(['string', 'int']);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testParenthesesAreAddedAroundTokensWhenNestedIsTurnedOn(): void
    {
        $token = new MultiTypeDef(['string', 'int'], nestedTypes: true);

        $this->assertEquals(
            <<<'EOS'
            (string|int)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testIfInnerTypeIsMultiTypeItGetsRenderedAtTheCorrectPlaceAndAllParenthesesAreRendered(): void
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
