<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use PHPUnit\Framework\TestCase;

class SingleTypeDefTest extends TestCase
{
    public function testTypeIsRenderedAsAnIdentifier()
    {
        $token = new SingleTypeDef('CrazyCodeGen\\Tokens\\Token');

        $this->assertEquals(new Token('CrazyCodeGen\\Tokens\\Token'), $token->getTokens(new RenderContext(), new RenderingRules())[0]);
    }

    public function testShortNameIsRenderedAsAnIdentifierWhenShortenIsTurnedOn()
    {
        $token = new SingleTypeDef('CrazyCodeGen\\Tokens\\Token');

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\\Tokens\\Token';

        $this->assertEquals(new Token('Token'), $token->getTokens($context, new RenderingRules())[0]);
    }

    public function testShortNameAndNamespaceAlwaysAvailableEvenWhenShortenIsOff()
    {
        $token = new SingleTypeDef('CrazyCodeGen\\Tokens\\Token');

        $this->assertEquals('Token', $token->getShortName());
        $this->assertEquals('CrazyCodeGen\\Tokens', $token->getNamespace());
    }

    public function testNamespaceIsNullWhenNoBackslashFound()
    {
        $token = new SingleTypeDef('Token');

        $this->assertEquals('Token', $token->getShortName());
        $this->assertNull($token->getNamespace());
    }
}
