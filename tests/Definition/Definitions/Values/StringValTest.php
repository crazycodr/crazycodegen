<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Values\StringVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class StringValTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testStringIsAlwaysRenderedWithSingleQuotes(): void
    {
        $token = new StringVal('Hello world');

        $this->assertEquals(
            <<<'EOS'
            'Hello world'
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testSingleQuotesAreEscaped(): void
    {
        $token = new StringVal('Hello world can\'t be escaped');

        $this->assertEquals(
            <<<'EOS'
            'Hello world can\'t be escaped'
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
