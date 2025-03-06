<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AmpersandToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PipeToken;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use PHPUnit\Framework\TestCase;

class MultiTypeTokenGroupTest extends TestCase
{
    public function testTypesAreJoinedWithPipeWhenUnionIsTrueByDefault()
    {
        $token = new MultiTypeTokenGroup([
            $stringType = new SingleTypeTokenGroup('string'),
            $intType = new SingleTypeTokenGroup('int'),
        ]);

        $this->assertEquals(
            array_merge(
                $stringType->render(new RenderContext(), new RenderingRules()),
                [new PipeToken()],
                $intType->render(new RenderContext(), new RenderingRules()),
            ),
            $token->render(new RenderContext(), new RenderingRules())
        );
    }

    public function testTypesAreJoinedWithAmpersandWhenUnionIsFalse()
    {
        $token = new MultiTypeTokenGroup(
            [
                $stringType = new SingleTypeTokenGroup('string'),
                $intType = new SingleTypeTokenGroup('int'),
            ],
            unionTypes: false
        );

        $this->assertEquals(
            array_merge(
                $stringType->render(new RenderContext(), new RenderingRules()),
                [new AmpersandToken()],
                $intType->render(new RenderContext(), new RenderingRules()),
            ),
            $token->render(new RenderContext(), new RenderingRules())
        );
    }

    public function testStringTypesAreConvertedToSingleTypeTokenGroupsAndRendered()
    {
        $token = new MultiTypeTokenGroup(['string', 'int']);

        $this->assertEquals(
            array_merge(
                (new SingleTypeTokenGroup('string'))->render(new RenderContext(), new RenderingRules()),
                [new PipeToken()],
                (new SingleTypeTokenGroup('int'))->render(new RenderContext(), new RenderingRules()),
            ),
            $token->render(new RenderContext(), new RenderingRules())
        );
    }

    public function testParenthesesAreAddedAroundTokensWhenNestedIsTurnedOn()
    {
        $token = new MultiTypeTokenGroup(['string', 'int'], nestedTypes: true);

        $this->assertEquals(
            array_merge(
                [new ParStartToken()],
                (new SingleTypeTokenGroup('string'))->render(new RenderContext(), new RenderingRules()),
                [new PipeToken()],
                (new SingleTypeTokenGroup('int'))->render(new RenderContext(), new RenderingRules()),
                [new ParEndToken()],
            ),
            $token->render(new RenderContext(), new RenderingRules())
        );
    }

    public function testIfInnerTypeIsMultiTypeItGetsRenderedAtTheCorrectPlaceAndAllParenthesesAreRendered()
    {
        $token = new MultiTypeTokenGroup(
            [
                $intFloatType = new MultiTypeTokenGroup(['int', 'float'], nestedTypes: true),
                $stringBoolType = new MultiTypeTokenGroup(['string', 'bool'], nestedTypes: true),
            ],
            unionTypes: false,
            nestedTypes: true,
        );

        $this->assertEquals(
            array_merge(
                [new ParStartToken()],
                $intFloatType->render(new RenderContext(), new RenderingRules()),
                [new AmpersandToken()],
                $stringBoolType->render(new RenderContext(), new RenderingRules()),
                [new ParEndToken()],
            ),
            $token->render(new RenderContext(), new RenderingRules())
        );
    }
}