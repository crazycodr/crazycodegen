<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Values\NullVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NullValTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testNullValReturnsNullToken(): void
    {
        $token = new NullVal();

        $this->assertEquals(
            <<<'EOS'
            null
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
