<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MultiTypeTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testTypesAreJoinedWithPipeWhenUnionIsTrueByDefault()
    {
        $token = new MultiTypeTokenGroup([
            new SingleTypeTokenGroup('string'),
            new SingleTypeTokenGroup('int'),
        ]);

        $this->assertEquals(<<<EOS
            string|int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTypesAreJoinedWithAmpersandWhenUnionIsFalse()
    {
        $token = new MultiTypeTokenGroup(
            [
                new SingleTypeTokenGroup('string'),
                new SingleTypeTokenGroup('int'),
            ],
            unionTypes: false
        );

        $this->assertEquals(<<<EOS
            string&int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testStringTypesAreConvertedToSingleTypeTokenGroupsAndRendered()
    {
        $token = new MultiTypeTokenGroup(['string', 'int']);

        $this->assertEquals(<<<EOS
            string|int
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testParenthesesAreAddedAroundTokensWhenNestedIsTurnedOn()
    {
        $token = new MultiTypeTokenGroup(['string', 'int'], nestedTypes: true);

        $this->assertEquals(<<<EOS
            (string|int)
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testIfInnerTypeIsMultiTypeItGetsRenderedAtTheCorrectPlaceAndAllParenthesesAreRendered()
    {
        $token = new MultiTypeTokenGroup(
            [
                new MultiTypeTokenGroup(['int', 'float'], nestedTypes: true),
                new MultiTypeTokenGroup(['string', 'bool'], nestedTypes: true),
            ],
            unionTypes: false,
            nestedTypes: true,
        );

        $this->assertEquals(<<<EOS
            ((int|float)&(string|bool))
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}