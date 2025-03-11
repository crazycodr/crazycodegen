<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class DocBlockDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testEmptyDocBlockIsNotRendered()
    {
        $token = new DocBlockDefinition(
            texts: [],
        );

        $this->assertEquals([], $token->render(new RenderContext(), new RenderingRules()));
    }

    public function testEmptyTextsAreIgnoredButCanStillGenerateEmptyDocBlock()
    {
        $token = new DocBlockDefinition(
            texts: ['', ''],
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             */
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTextsAreGeneratedWithEmptyLineBetweenThemAndEmptyLineDoesNotFeatureTrailingSpace()
    {
        $token = new DocBlockDefinition(
            texts: ['Hello', 'World', 'Foo'],
        );

        $this->assertEquals(
            <<<'EOS'
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
        $token = new DocBlockDefinition(
            texts: ['Hello world, i love programming and this automatically wraps on 25 characters.'],
        );

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 25;

        $this->assertEquals(
            <<<'EOS'
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

    public function testLongTextsWithoutSpacesFoundAreScannedForTheNextSpaceAsLongAsNeeded()
    {
        $token = new DocBlockDefinition(
            texts: ['https://example.com/questions/49907308/url-without-spaces will chop here.'],
        );

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 25;

        $this->assertEquals(
            <<<'EOS'
            /**
             * https://example.com/questions/49907308/url-without-spaces
             * will chop here.
             */
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testLongTextsWithoutSpacesFoundAreScannedForTheNextSpaceButWillTakeAllIfNothingLeftAfterIt()
    {
        $token = new DocBlockDefinition(
            texts: ['Upcoming url is too long so it will be take as a whole: https://example.com/questions/49907308/url-without-spaces'],
        );

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 25;

        $this->assertEquals(
            <<<'EOS'
            /**
             * Upcoming url is too long
             * so it will be take as a
             * whole:
             * https://example.com/questions/49907308/url-without-spaces
             */
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}
