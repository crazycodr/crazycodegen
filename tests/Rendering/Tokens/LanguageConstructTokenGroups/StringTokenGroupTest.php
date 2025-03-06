<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\StringTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class StringTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testStringIsAlwaysRenderedWithSingleQuotes()
    {
        $token = new StringTokenGroup('Hello world');

        $this->assertEquals(<<<EOS
            'Hello world'
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }

    public function testSingleQuotesAreEscaped()
    {
        $token = new StringTokenGroup('Hello world can\'t be escaped');

        $this->assertEquals(<<<EOS
            'Hello world can\'t be escaped'
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}