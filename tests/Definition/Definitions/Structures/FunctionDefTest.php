<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\FunctionDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class FunctionDefTest extends TestCase
{
    use TokenFunctions;

    public function testFunctionRendersAsExpected()
    {
        $token = new FunctionDef(
            name: 'myFunction',
        );

        $this->assertEquals(
            <<<'EOS'
            function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testArgumentsAreRenderedAsExpected()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            parameters: [
                new ParameterDef(name: 'foo'),
                new ParameterDef(name: 'bar', type: 'int'),
                new ParameterDef(name: 'baz', type: 'bool', defaultValue: true),
            ]
        );


        $this->assertEquals(
            <<<'EOS'
            function myFunction($foo,int $bar,bool $baz=true){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testReturnTypeRenderedAsExpected()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            returnType: 'string',
        );


        $this->assertEquals(
            <<<'EOS'
            function myFunction():string{}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    public function testDocBlockIsProperlyRendered()
    {
        $token = new FunctionDef(
            name: 'myFunction',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the function declaration.']),
        );


        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be wrapped and displayed before the function
             * declaration.
             */
            function myFunction(){}
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
