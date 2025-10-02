<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ClassTypeDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testTypeIsRenderedAsExpected(): void
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');
        $this->assertEquals(
            <<<'EOS'
            CrazyCodeGen\Tokens\Token
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testShortNameIsRenderedAsAnIdentifierWhenShortenIsTurnedOn(): void
    {
        $token = new ClassTypeDef('CrazyCodeGen\\Tokens\\Token');
        $context = new RenderingContext();
        $context->importedClasses[] = 'CrazyCodeGen\\Tokens\\Token';

        $this->assertEquals(
            <<<'EOS'
            Token
            EOS,
            $this->renderTokensToString($token->getTokens($context))
        );
    }

    public function testCanResolveClassReference(): void
    {
        $token = new ClassTypeDef('Token');

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
