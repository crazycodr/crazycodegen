<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Values\FloatVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FloatValTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testFloatValReturnsFloatPassedIn(): void
    {
        $token = new FloatVal(81.3335698);

        $this->assertEquals(
            <<<'EOS'
            81.3335698
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testFloatValSupportsNegative(): void
    {
        $token = new FloatVal(-65.741855);

        $this->assertEquals(
            <<<'EOS'
            -65.741855
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
