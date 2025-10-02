<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Definitions\Values\IntVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class IntValTest extends TestCase
{
    use TokenFunctions;

    public function testIntValReturnsIntegerPassedIn()
    {
        $token = new IntVal(17);

        $this->assertEquals(
            <<<'EOS'
            17
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testIntValSupportsNegative()
    {
        $token = new IntVal(-33);

        $this->assertEquals(
            <<<'EOS'
            -33
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
