<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class BuiltInTypeSpecTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    /**
     * Not testing all possible cases
     */
    public function testReturnTheExpectedTokensPerType(): void
    {
        $token = new BuiltInTypeSpec(BuiltInTypesEnum::int);
        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );

        $token = new BuiltInTypeSpec(BuiltInTypesEnum::bool);
        $this->assertEquals(
            <<<'EOS'
            bool
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );

        $token = new BuiltInTypeSpec(BuiltInTypesEnum::false);
        $this->assertEquals(
            <<<'EOS'
            false
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );

        $token = new BuiltInTypeSpec(BuiltInTypesEnum::mixed);
        $this->assertEquals(
            <<<'EOS'
            mixed
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
