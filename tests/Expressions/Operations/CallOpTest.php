<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class CallOpTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testSubjectAndFunctionAreConvertedToTokensWhenStrings(): void
    {
        $token = new CallOp(
            subject: 'setUp',
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testFunctionConvertedToTokenWhenMethodTokenGroupPassedIn(): void
    {
        $token = new CallOp(
            subject: new MethodDef(name: 'setUp'),
        );

        $this->assertEquals(
            <<<'EOS'
            setUp()
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokenGroupArgumentAsExpected(): void
    {
        $token = new CallOp(
            subject: new MethodDef(name: 'setUp'),
            arguments: [new VariableDef('bar')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp($bar)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokensArgumentAsListOfItemsSeparatedByCommasAndSpaces(): void
    {
        $token = new CallOp(
            subject: new MethodDef(name: 'setUp'),
            arguments: [new Token('1'), new Token('2'), new Token('3')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp(1,2,3)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpaces(): void
    {
        $token = new CallOp(
            subject: new MethodDef(name: 'setUp'),
            arguments: [new VariableDef('bar'), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp($bar,$baz)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testRendersTokenGroupsArgumentAsListOfItemsSeparatedByCommasAndSpacesAndUsesTheInlineVersions(): void
    {
        $token = new CallOp(
            subject: new MethodDef(name: 'setUp'),
            arguments: [new ArrayVal([1, 2, 3]), new VariableDef('baz')],
        );

        $this->assertEquals(
            <<<'EOS'
            setUp([1,2,3],$baz)
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
