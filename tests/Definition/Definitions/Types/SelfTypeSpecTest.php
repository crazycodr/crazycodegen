<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\SelfTypeSpec;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class SelfTypeSpecTest extends TestCase
{
    use TokenFunctions;

    public function testTypeIsRenderedAsExpected()
    {
        $token = new SelfTypeSpec();
        $this->assertEquals(
            <<<'EOS'
            self
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testCanResolveClassReference()
    {
        $token = new SelfTypeSpec();

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
