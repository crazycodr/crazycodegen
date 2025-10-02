<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class MethodDefTest extends TestCase
{
    use TokenFunctions;

    public function testDeclarationRendersAbstractKeyword()
    {
        $token = new MethodDef(
            name: 'myFunction',
            abstract: true,
        );

        $this->assertEquals(
            <<<'EOS'
            abstract public function myFunction();
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testDeclarationRendersPublicVisibilityByDefault()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testDeclarationRendersStaticKeyword()
    {
        $token = new MethodDef(
            name: 'myFunction',
            static: true,
        );

        $this->assertEquals(
            <<<'EOS'
            public static function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testDeclarationRendersVisibility()
    {
        $token = new MethodDef(
            name: 'myFunction',
            visibility: VisibilityEnum::PROTECTED,
        );

        $this->assertEquals(
            <<<'EOS'
            protected function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testInlineDefinitionRendersFunctionKeyword()
    {
        $token = new MethodDef(
            name: 'myFunction',
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testRendersArgumentListAsExpected()
    {
        $token = new MethodDef(
            name: 'myFunction',
            parameters: [
                new ParameterDef(name: 'foo'),
                new ParameterDef(name: 'bar', type: 'int'),
                new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction($foo,int $bar,bool $baz=true){}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testRendersReturnTypeAsExpected()
    {
        $token = new MethodDef(
            name: 'myFunction',
            returnType: 'string',
        );

        $this->assertEquals(
            <<<'EOS'
            public function myFunction():string{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testDocBlockIsProperlyRendered()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testInstructionsAreRenderedAsExpected()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
