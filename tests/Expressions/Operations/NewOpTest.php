<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Definition\Expressions\Operations\NewOp;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NewOpTest extends TestCase
{
    use TokenFunctions;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testNewKeywordClassnameAndEmptyParenthesesArePresent()
    {
        $token = new NewOp(
            class: new Expression('foo'),
        );

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testStringClassNameIsConvertedAndOutputAsExpected()
    {
        $token = new NewOp(
            class: 'foo'
        );

        $this->assertEquals(
            <<<'EOS'
            new \foo()
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     * @throws InvalidIdentifierFormatException
     */
    public function testClassTokenizerGetConvertedToSimpleClassName()
    {
        $token = new NewOp(
            class: new ClassDef(name: 'foo', namespace: new NamespaceDef('CrazyCodeGen\Tests')),
        );

        $this->assertEquals(
            <<<'EOS'
            new CrazyCodeGen\Tests\foo()
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testClassTokenizerGetShortNameRenderedWhenClassIsImportedInContext()
    {
        $token = new NewOp(
            class: new ClassDef(name: 'foo', namespace: new NamespaceDef('CrazyCodeGen\Tests')),
        );

        $context = new TokenizationContext();
        $context->importedClasses[] = 'CrazyCodeGen\Tests\foo';

        $this->assertEquals(
            <<<'EOS'
            new foo()
            EOS,
            $this->renderTokensToString($token->getSimpleTokens($context))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersStringArgumentAsSingleExpression()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [1],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1)
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokenizerArgumentAsExpected()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [new VariableDef('bar')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar)
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersExpressionsArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [new Expression(1), new Expression(2), new Expression(3)],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo(1,2,3)
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokenizersArgumentAsListOfItemsSeparatedByCommasAndSpaces()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [new VariableDef('bar'), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo($bar,$baz)
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokenizersArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions()
    {
        $token = new NewOp(
            class: new Expression('foo'),
            arguments: [new ArrayVal([1, 2, 3]), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            new foo([1,2,3],$baz)
            EOS,
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }
}
