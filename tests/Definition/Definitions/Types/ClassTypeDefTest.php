<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ClassTypeDefTest extends TestCase
{
    use TokenFunctions;

    public function testTypeIsRenderedAsExpected()
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');
        $this->assertEquals(
            <<<'EOS'
            CrazyCodeGen\Tokens\Token
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testShortNameIsRenderedAsAnIdentifierWhenShortenIsTurnedOn()
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');
        $context = new RenderContext();
        $context->importedClasses[] = 'CrazyCodeGen\\Tokens\\Token';

        $this->assertEquals(
            <<<'EOS'
            Token
            EOS,
            $this->renderTokensToString($token->getTokens($context, new RenderingRules()))
        );
    }

    public function testCanResolveClassReference()
    {
        $token = new ClassTypeDef('Token');

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
