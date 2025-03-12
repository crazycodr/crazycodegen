<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Definitions\Values\StringVal;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class StringTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testStringIsAlwaysRenderedWithSingleQuotes()
    {
        $token = new StringVal('Hello world');

        $this->assertEquals(
            <<<'EOS'
            'Hello world'
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testSingleQuotesAreEscaped()
    {
        $token = new StringVal('Hello world can\'t be escaped');

        $this->assertEquals(
            <<<'EOS'
            'Hello world can\'t be escaped'
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
