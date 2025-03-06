<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use PHPUnit\Framework\TestCase;

class SingleTypeTokenGroupTest extends TestCase
{
    public function testTypeIsRenderedAsAnIdentifier()
    {
        $token = new SingleTypeTokenGroup('CrazyCodeGen\\Tokens\\Token');

        $this->assertEquals(new IdentifierToken('CrazyCodeGen\\Tokens\\Token'), $token->render(new RenderContext(), new RenderingRules())[0]);
    }

    public function testShortNameIsRenderedAsAnIdentifierWhenShortenIsTurnedOn()
    {
        $token = new SingleTypeTokenGroup('CrazyCodeGen\\Tokens\\Token');

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\\Tokens\\Token';

        $this->assertEquals(new IdentifierToken('Token'), $token->render($context, new RenderingRules())[0]);
    }

    public function testShortNameAndNamespaceAlwaysAvailableEvenWhenShortenIsOff()
    {
        $token = new SingleTypeTokenGroup('CrazyCodeGen\\Tokens\\Token');

        $this->assertEquals('Token', $token->getShortName());
        $this->assertEquals('CrazyCodeGen\\Tokens', $token->getNamespace());
    }

    public function testNamespaceIsNullWhenNoBackslashFound()
    {
        $token = new SingleTypeTokenGroup('Token');

        $this->assertEquals('Token', $token->getShortName());
        $this->assertNull($token->getNamespace());
    }
}
