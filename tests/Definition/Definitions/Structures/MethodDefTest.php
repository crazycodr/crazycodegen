<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MethodDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testDeclarationRendersAbstractKeyword(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            abstract: true,
        );

        $this->assertEquals(
            <<<'EOS'
            abstract public function myFunction();
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testDeclarationRendersPublicVisibilityByDefault(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testDeclarationRendersStaticKeyword(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            static: true,
        );

        $this->assertEquals(
            <<<'EOS'
            public static function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testDeclarationRendersVisibility(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            visibility: VisibilityEnum::PROTECTED,
        );

        $this->assertEquals(
            <<<'EOS'
            protected function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testInlineDefinitionRendersFunctionKeyword(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testConstructorAcceptsPropertyDefAndRendersThemAsExpected(): void
    {
        $token = new MethodDef(
            name: '__construct',
            parameters: [
                new ParameterDef(name: 'foo'),
                new PropertyDef(name: 'bar', type: BuiltInTypeSpec::intType()),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            public function __construct($foo,public int $bar){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testNonConstructorMethodRefusesToAcceptPropertyDefs(): void
    {
        $this->expectException(NoValidConversionRulesMatchedException::class);
        new MethodDef(
            name: 'notAConstructor',
            parameters: [
                new ParameterDef(name: 'foo'),
                new PropertyDef(name: 'bar', type: BuiltInTypeSpec::intType()),
            ],
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testRendersArgumentListAsExpected(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: [
                new ParameterDef(name: 'foo'),
                new ParameterDef(name: 'bar', type: BuiltInTypeSpec::intType()),
                new ParameterDef(name: 'baz', type: BuiltInTypeSpec::boolType(), defaultValue: true),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction($foo,int $bar,bool $baz=true){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testRendersReturnTypeAsExpected(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: BuiltInTypeSpec::stringType(),
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction():string{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testDocBlockIsProperlyRendered(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the function declaration.']),
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be wrapped and displayed before the function
             * declaration.
             */
            public function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testInstructionsAreRenderedAsExpected(): void
    {
        $token = new MethodDef(
            name: 'myFunction',
            instructions: [
                new Instruction(
                    expressions: [
                        new Expression('1 === (1*3)')
                    ],
                ),
                new Instruction(
                    expressions: [
                        new Expression('return 1'),
                    ],
                ),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(){1 === (1*3);return 1;}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
