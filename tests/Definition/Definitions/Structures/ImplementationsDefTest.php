<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ImplementationsDef;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImplementationsDefTest extends TestCase
{
    use TokenFunctions;

    public function testRenderKeywordsAsExpected()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $this->assertEquals(
            'implements \\JsonSerializable,\\ArrayAccess',
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
