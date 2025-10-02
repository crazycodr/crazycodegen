<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\ConstantDef;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasImportsTraitTestTrait;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasNamespaceTraitTestTrait;
use CrazyCodeGen\Tests\Definition\Definitions\Traits\HasNameTraitTestTrait;
use PHPUnit\Framework\TestCase;

class ClassDefTest extends TestCase
{
    use TokenFunctions;
    use HasNamespaceTraitTestTrait;
    use HasImportsTraitTestTrait;
    use HasNameTraitTestTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function getHasNamespaceTraitTestObject(NamespaceDef|string|null $namespace): ClassDef
    {
        return new ClassDef(name: 'valid', namespace: $namespace);
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

    public function testClassKeywordIsRendered()
    {
        $token = new ClassDef(
            name: 'myClass',
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testClassNameIsRendered()
    {
        $token = new ClassDef(
            name: 'myClass',
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testAbstractKeywordIsRendered()
    {
        $token = new ClassDef(
            name: 'myClass',
            abstract: true,
        );

        $this->assertEquals(
            <<<'EOS'
            abstract class myClass{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNamespaceIsRendered()
    {
        $token = new ClassDef(
            name: 'myClass',
            namespace: new NamespaceDef('CrazyCodeGen\Tests'),
        );

        $this->assertEquals(
            <<<'EOS'
            namespace CrazyCodeGen\Tests;class myClass{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testDocBlockIsProperlyRendered()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testExtendsIsRendered()
    {
        $token = new ClassDef(
            name: 'myClass',
            extends: 'CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit',
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass extends CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testImplementsAreProperlyRendered()
    {
        $token = new ClassDef(
            name: 'myClass',
            implementations: [
                'CrazyCodeGen\Tests',
                'CrazyCodeGen\Tests2',
                'CrazyCodeGen\Tests3',
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass implements CrazyCodeGen\Tests,CrazyCodeGen\Tests2,CrazyCodeGen\Tests3{}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testConstantsAreRenderedAsExpected()
    {
        $token = new ClassDef(
            name: 'myClass',
            constants: [
                new ConstantDef(name: 'const1', defaultValue: 1),
                new ConstantDef(name: 'const2', defaultValue: 2),
            ],
        );

        $this->assertEquals(
            <<<'EOS'
            class myClass{public const $const1=1;public const $const2=2;}
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testPropertiesAsExpected()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testMethodsAreRenderedAsExpected()
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
