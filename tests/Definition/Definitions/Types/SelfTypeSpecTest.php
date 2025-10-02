<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\SelfTypeSpec;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class SelfTypeSpecTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testTypeIsRenderedAsExpected(): void
    {
        $token = new SelfTypeSpec();
        $this->assertEquals(
            <<<'EOS'
            self
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testCanResolveClassReference(): void
    {
        $token = new SelfTypeSpec();

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
