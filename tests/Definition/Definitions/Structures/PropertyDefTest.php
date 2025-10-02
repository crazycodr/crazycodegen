<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\MultiTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class PropertyDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testRendersVisibilityAndName(): void
    {
        $token = new PropertyDef(
            name: 'foo',
        );

        $this->assertEquals(
            <<<'EOS'
            public $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersNameFromStringAsExpected(): void
    {
        $token = new PropertyDef(
            name: 'foo',
        );

        $this->assertEquals(
            <<<'EOS'
            public $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testDifferentVisibilityPropertyRendered(): void
    {
        $token = new PropertyDef(
            name: 'foo',
            visibility: VisibilityEnum::PROTECTED,
        );

        $this->assertEquals(
            <<<'EOS'
            protected $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersStaticModifierWithSpaces(): void
    {
        $token = new PropertyDef(
            name: 'foo',
            static: true
        );

        $this->assertEquals(
            <<<'EOS'
            public static $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersType(): void
    {
        $token = new PropertyDef(
            name: 'foo',
            type: new BuiltInTypeSpec(BuiltInTypesEnum::int)
        );

        $this->assertEquals(
            <<<'EOS'
            public int $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersComplexTypeAsExpected(): void
    {
        $token = new PropertyDef(
            name: 'foo',
            type: new MultiTypeDef(types: [
                new BuiltInTypeSpec(BuiltInTypesEnum::int),
                new BuiltInTypeSpec(BuiltInTypesEnum::string),
                new BuiltInTypeSpec(BuiltInTypesEnum::bool),
            ])
        );

        $this->assertEquals(
            <<<'EOS'
            public int|string|bool $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testDefaultValueRendersAfterNameWithExpectedSpaces(): void
    {
        $token = new PropertyDef(
            name: 'foo',
            defaultValue: 'Hello world'
        );

        $this->assertEquals(
            <<<'EOS'
            public $foo='Hello world';
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testDocBlockIsProperlyRendered(): void
    {
        $token = new PropertyDef(
            name: 'prop1',
            docBlock: new DocBlockDef(['This is a docblock that should be displayed before the prop.']),
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be displayed before the prop.
             */
            public $prop1;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
