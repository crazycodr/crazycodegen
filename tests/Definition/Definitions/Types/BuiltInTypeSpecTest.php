<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
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
        $token = BuiltInTypeSpec::intType();
        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );

        $token = BuiltInTypeSpec::boolType();
        $this->assertEquals(
            <<<'EOS'
            bool
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );

        $token = BuiltInTypeSpec::falseType();
        $this->assertEquals(
            <<<'EOS'
            false
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );

        $token = BuiltInTypeSpec::mixedType();
        $this->assertEquals(
            <<<'EOS'
            mixed
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testStaticConstructorsReturnCorrectInstance(): void
    {
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::intType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::floatType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::boolType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::stringType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::arrayType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::objectType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::callableType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::voidType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::trueType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::falseType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::nullType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::mixedType());
        $this->assertInstanceOf(BuiltInTypeSpec::class, BuiltInTypeSpec::iterableType());
    }
}
