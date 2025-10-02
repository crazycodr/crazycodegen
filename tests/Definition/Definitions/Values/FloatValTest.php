<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Definitions\Values\FloatVal;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FloatValTest extends TestCase
{
    use TokenFunctions;

    public function testFloatValReturnsFloatPassedIn()
    {
        $token = new FloatVal(81.3335698);

        $this->assertEquals(
            <<<'EOS'
            81.3335698
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testFloatValSupportsNegative()
    {
        $token = new FloatVal(-65.741855);

        $this->assertEquals(
            <<<'EOS'
            -65.741855
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
