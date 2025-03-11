<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\MultiTypeDefinition;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MultiTypeDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testTypesAreJoinedWithPipeWhenUnionIsTrueByDefault()
    {
        $token = new MultiTypeDefinition([
            new SingleTypeDefinition('string'),
            new SingleTypeDefinition('int'),
        ]);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTypesAreJoinedWithAmpersandWhenUnionIsFalse()
    {
        $token = new MultiTypeDefinition(
            [
                new SingleTypeDefinition('string'),
                new SingleTypeDefinition('int'),
            ],
            unionTypes: false
        );

        $this->assertEquals(
            <<<'EOS'
            string&int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testStringTypesAreConvertedToSingleTypeTokenGroupsAndRendered()
    {
        $token = new MultiTypeDefinition(['string', 'int']);

        $this->assertEquals(
            <<<'EOS'
            string|int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testParenthesesAreAddedAroundTokensWhenNestedIsTurnedOn()
    {
        $token = new MultiTypeDefinition(['string', 'int'], nestedTypes: true);

        $this->assertEquals(
            <<<'EOS'
            (string|int)
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testIfInnerTypeIsMultiTypeItGetsRenderedAtTheCorrectPlaceAndAllParenthesesAreRendered()
    {
        $token = new MultiTypeDefinition(
            [
                new MultiTypeDefinition(['int', 'float'], nestedTypes: true),
                new MultiTypeDefinition(['string', 'bool'], nestedTypes: true),
            ],
            unionTypes: false,
            nestedTypes: true,
        );

        $this->assertEquals(
            <<<'EOS'
            ((int|float)&(string|bool))
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}
