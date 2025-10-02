<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\StaticTypeSpec;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class StaticTypeSpecTest extends TestCase
{
    use TokenFunctions;

    public function testTypeIsRenderedAsExpected()
    {
        $token = new StaticTypeSpec();
        $this->assertEquals(
            <<<'EOS'
            static
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testCanResolveClassReference()
    {
        $token = new StaticTypeSpec();

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
