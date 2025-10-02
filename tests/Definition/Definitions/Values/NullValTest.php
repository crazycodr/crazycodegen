<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Definitions\Values\NullVal;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NullValTest extends TestCase
{
    use TokenFunctions;

    public function testNullValReturnsNullToken()
    {
        $token = new NullVal();

        $this->assertEquals(
            <<<'EOS'
            null
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
