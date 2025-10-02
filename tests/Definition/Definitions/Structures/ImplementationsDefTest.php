<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\ImplementationsDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImplementationsDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testRenderKeywordsAsExpected(): void
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $this->assertEquals(
            'implements \\JsonSerializable,\\ArrayAccess',
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
