<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\ConstantDef;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Structures\UseTraitDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasImportsTraitTestTrait;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasNamespaceTraitTestTrait;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasNameTraitTestTrait;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasTraitsTestTrait;
use PHPUnit\Framework\TestCase;

class ClassDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;
    use HasNamespaceTraitTestTrait;
    use HasImportsTraitTestTrait;
    use HasNameTraitTestTrait;
    use HasTraitsTestTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    protected function getHasNamespaceTraitTestObject(?NamespaceDef $namespace): ClassDef
    {
        return new ClassDef(name: 'valid', namespace: $namespace);
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    protected function getHasTraitsTraitTestObject(string|ClassTypeDef|UseTraitDef $trait): ClassDef
    {
        return new ClassDef(name: 'valid', traits: [$trait]);
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function getHasImportsTraitTestObject(array $imports): ClassDef
    {
        return new ClassDef(name: 'valid', imports: $imports);
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function getHasNameTraitTestObject(string $identifier): ClassDef
    {
        return new ClassDef(name: $identifier);
    }

    public function testClassKeywordIsRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testClassNameIsRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testAbstractKeywordIsRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            abstract: true,
        );

        $this->assertEquals(
            <<<'EOS'
            abstract class myClass{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNamespaceIsRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            namespace: new NamespaceDef('CrazyCodeGen\Tests'),
        );

        $this->assertEquals(
            <<<'EOS'
            namespace CrazyCodeGen\Tests;class myClass{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testDocBlockIsProperlyRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the class declaration.']),
        );

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be wrapped and displayed before the class
             * declaration.
             */
            class myClass{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testExtendsIsRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            extends: new ClassTypeDef('CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit'),
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass extends CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testImplementsAreProperlyRendered(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            implementations: [
                new ClassTypeDef('CrazyCodeGen\Tests'),
                new ClassTypeDef('CrazyCodeGen\Tests2'),
                new ClassTypeDef('CrazyCodeGen\Tests3'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass implements CrazyCodeGen\Tests,CrazyCodeGen\Tests2,CrazyCodeGen\Tests3{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testTraitsAreRenderedAsExpected(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            traits: [
                new UseTraitDef('CrazyCodeGen\Tests2\TraitA'),
                new UseTraitDef('CrazyCodeGen\Tests2\TraitB'),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{use CrazyCodeGen\Tests2\TraitA;use CrazyCodeGen\Tests2\TraitB;}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testConstantsAreRenderedAsExpected(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            constants: [
                new ConstantDef(name: 'const1', value: 1),
                new ConstantDef(name: 'const2', value: 2),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{public const $const1=1;public const $const2=2;}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testPropertiesAsExpected(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            properties: [
                new PropertyDef(name: 'prop1'),
                new PropertyDef(name: 'prop2'),
                new PropertyDef(name: 'prop3'),
            ]
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{public $prop1;public $prop2;public $prop3;}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testMethodsAreRenderedAsExpected(): void
    {
        $token = new ClassDef(
            name: 'myClass',
            methods: [
                new MethodDef(name: 'method1'),
                new MethodDef(name: 'method2'),
                new MethodDef(name: 'method3'),
            ]
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{public function method1(){}public function method2(){}public function method3(){}}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
