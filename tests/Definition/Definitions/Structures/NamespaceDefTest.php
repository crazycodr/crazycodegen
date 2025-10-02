<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NamespaceDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testNamespaceRenderedAsExpected(): void
    {
        $token = new NamespaceDef('CrazyCodeGen\\Tests');

        $this->assertEquals(
            <<<'EOS'
            namespace CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
