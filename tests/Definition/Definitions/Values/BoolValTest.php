<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Values\BoolVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class BoolValTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testBoolValReturnsTrueTokenWhenTrue(): void
    {
        $token = new BoolVal(true);

        $this->assertEquals(
            <<<'EOS'
            true
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testBoolValReturnsFalseTokenWhenFalse(): void
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
