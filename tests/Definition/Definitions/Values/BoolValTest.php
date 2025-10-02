<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Definitions\Values\BoolVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class BoolValTest extends TestCase
{
    use TokenFunctions;

    public function testBoolValReturnsTrueTokenWhenTrue()
    {
        $token = new BoolVal(true);

        $this->assertEquals(
            <<<'EOS'
            true
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testBoolValReturnsFalseTokenWhenFalse()
    {
        $token = new BoolVal(false);

        $this->assertEquals(
            <<<'EOS'
            false
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
