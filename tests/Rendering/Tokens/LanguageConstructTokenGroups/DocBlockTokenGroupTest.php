<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\DocBlockTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class DocBlockTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testEmptyDocBlockIsNotRendered()
    {
        $token = new DocBlockTokenGroup(
            texts: [],
        );

        $this->assertEquals([], $token->render(new RenderContext(), new RenderingRules()));
    }

    public function testEmptyTextsAreIgnoredButCanStillGenerateEmptyDocBlock()
    {
        $token = new DocBlockTokenGroup(
            texts: ['', ''],
        );

        $this->assertEquals(<<<EOS
            /**
             */
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTextsAreGeneratedWithEmptyLineBetweenThemAndEmptyLineDoesNotFeatureTrailingSpace()
    {
        $token = new DocBlockTokenGroup(
            texts: ['Hello', 'World', 'Foo'],
        );

        $this->assertEquals(<<<EOS
            /**
             * Hello
             *
             * World
             *
             * Foo
             */
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testLongTextsAreWrappedBasedOnLineLengthAndSplitsOnSpacesOnlyAndExtraSpacesAreTrimmedOnSplit()
    {
        $token = new DocBlockTokenGroup(
            texts: ['Hello world, i love programming and this automatically wraps on 25 characters.'],
        );

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 25;

        $this->assertEquals(<<<EOS
            /**
             * Hello world, i love
             * programming and this
             * automatically wraps on 25
             * characters.
             */
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}