<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class DocBlockDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testEmptyDocBlockIsNotRendered(): void
    {
        $token = new DocBlockDef(
            texts: [],
        );

        $this->assertEquals([], $token->getTokens(new RenderingContext()));
    }

    public function testEmptyTextsAreIgnoredButCanStillGenerateEmptyDocBlock(): void
    {
        $token = new DocBlockDef(
            texts: ['', ''],
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             */
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTextsAreGeneratedWithEmptyLineBetweenThemAndEmptyLineDoesNotFeatureTrailingSpace(): void
    {
        $token = new DocBlockDef(
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
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testLongTextsAreWrappedBasedOnLineLengthAndSplitsOnSpacesOnlyAndExtraSpacesAreTrimmedOnSplit(): void
    {
        $token = new DocBlockDef(
            texts: ['Hello world, i love programming and this long comment will automatically wrap on the 80th characters.'],
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             * Hello world, i love programming and this long comment will automatically wrap on
             * the 80th characters.
             */
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testLongTextsWithoutSpacesFoundAreScannedForTheNextSpaceAsLongAsNeeded(): void
    {
        $token = new DocBlockDef(
            texts: ['https://example.com/long-example-of-a-url-feature-a-question/49907308/url-without-spaces will chop here.'],
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             * https://example.com/long-example-of-a-url-feature-a-question/49907308/url-without-spaces
             * will chop here.
             */
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testLongTextsWithoutSpacesFoundAreScannedForTheNextSpaceButWillTakeAllIfNothingLeftAfterIt(): void
    {
        $token = new DocBlockDef(
            texts: ['Upcoming url is too long so it will be take as a whole: https://example.com/questions/49907308/url-without-spaces'],
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             * Upcoming url is too long so it will be take as a whole:
             * https://example.com/questions/49907308/url-without-spaces
             */
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
