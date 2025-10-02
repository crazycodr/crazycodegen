<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Types\SelfTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\StaticTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class TypeInferenceTraitTest extends TestCase
{
    use TokenFunctions;
    use TypeInferenceTrait;
    use FlattenFunction;

    public function testBuiltInTypeIsInferredAsExpected(): void
    {
        $type = $this->inferType('int');
        $this->assertInstanceOf(BuiltInTypeSpec::class, $type);
        $this->assertEquals(
            <<<'EOS'
            int
            EOS,
            $this->renderTokensToString($type->getTokens(new RenderingContext()))
        );
    }

    public function testStaticOrSelfTypesAreInferredAsExpected(): void
    {
        $type = $this->inferType('static');
        $this->assertInstanceOf(StaticTypeSpec::class, $type);
        $this->assertEquals(
            <<<'EOS'
            static
            EOS,
            $this->renderTokensToString($type->getTokens(new RenderingContext()))
        );

        $type = $this->inferType('self');
        $this->assertInstanceOf(SelfTypeSpec::class, $type);
        $this->assertEquals(
            <<<'EOS'
            self
            EOS,
            $this->renderTokensToString($type->getTokens(new RenderingContext()))
        );
    }

    public function testOtherStringIsInferredAsExpected(): void
    {
        $type = $this->inferType('Foo\\Bar\\Baz');
        $this->assertInstanceOf(ClassTypeDef::class, $type);
        $this->assertEquals(
            <<<'EOS'
            Foo\Bar\Baz
            EOS,
            $this->renderTokensToString($type->getTokens(new RenderingContext()))
        );
    }
}
