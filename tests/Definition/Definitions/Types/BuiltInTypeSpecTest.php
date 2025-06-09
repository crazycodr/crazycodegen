<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class BuiltInTypeSpecTest extends TestCase
{
    use TokenFunctions;

    public function testBuiltInTypeReplacedWithStringWhenNotSupported()
    {
        $type = new BuiltInTypeSpec('unknown');
        $this->assertEquals('string', $type->scalarType);
    }

    public function testSupportsReturnsTrueForSupportedType()
    {
        $this->assertTrue(BuiltInTypeSpec::supports('int'));
    }

    public function testSupportsReturnsFalseForUnsupportedType()
    {
        $this->assertFalse(BuiltInTypeSpec::supports('unknown'));
    }

    /**
     * Not testing all possible cases
     */
    public function testGetTokensReturnTheExpectedTokensPerType()
    {
        $token = new BuiltInTypeSpec('int');
        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );

        $token = new BuiltInTypeSpec('bool');
        $this->assertEquals(
            <<<'EOS'
            bool
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );

        $token = new BuiltInTypeSpec('false');
        $this->assertEquals(
            <<<'EOS'
            false
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );

        $token = new BuiltInTypeSpec('mixed');
        $this->assertEquals(
            <<<'EOS'
            mixed
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
