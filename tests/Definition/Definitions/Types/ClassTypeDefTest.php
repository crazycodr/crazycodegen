<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use PHPUnit\Framework\TestCase;

class ClassTypeDefTest extends TestCase
{
    public function testTypeIsRenderedAsAnIdentifier()
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');

        $this->assertEquals(new Token('CrazyCodeGen\\Tokens\\Token'), $token->getTokens(new RenderContext(), new RenderingRules())[0]);
    }

    public function testShortNameIsRenderedAsAnIdentifierWhenShortenIsTurnedOn()
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');

        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\\Tokens\\Token';

        $this->assertEquals(new Token('Token'), $token->getTokens($context, new RenderingRules())[0]);
    }

    public function testShortNameAndNamespaceAlwaysAvailableEvenWhenShortenIsOff()
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');

        $this->assertEquals('Token', $token->getShortName());
        $this->assertEquals('CrazyCodeGen\\Tokens', $token->getNamespace());
    }

    public function testNamespaceIsNullWhenNoBackslashFound()
    {
        $token = new ClassTypeDef('Token');

        $this->assertEquals('Token', $token->getShortName());
        $this->assertNull($token->getNamespace());
    }

    public function testCanResolveClassReference()
    {
        $token = new ClassTypeDef('Token');

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
