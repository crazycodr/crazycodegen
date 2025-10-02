<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NamespaceDefTest extends TestCase
{
    use TokenFunctions;

    public function testNamespaceRenderedAsExpected()
    {
        $token = new NamespaceDef('CrazyCodeGen\\Tests');

        $this->assertEquals(
            <<<'EOS'
            namespace CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
