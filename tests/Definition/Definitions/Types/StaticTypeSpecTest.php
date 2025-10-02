<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\StaticTypeSpec;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class StaticTypeSpecTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testTypeIsRenderedAsExpected(): void
    {
        $token = new StaticTypeSpec();
        $this->assertEquals(
            <<<'EOS'
            static
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testCanResolveClassReference(): void
    {
        $token = new StaticTypeSpec();

        $this->assertEquals(new ClassRefVal($token), $token->getClassReference());
    }
}
