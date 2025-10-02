<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParameterDefTest extends TestCase
{
    use TokenFunctions;

    public function testRendersNameAsExpectedWithoutSpacesAround()
    {
        $token = new ParameterDef(
            'foo'
        );


        $this->assertEquals(
            '$foo',
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testAddsTypeInFrontOfIdentifierAndSeparatesWithSpace()
    {
        $token = new ParameterDef(
            'foo',
            new BuiltInTypeSpec('int'),
        );

        $this->assertEquals(
            'int $foo',
            $this->renderTokensToString($token->getTokens(new RenderingContext())),
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testAddsDefaultValueAfterIdentifier()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 123,
        );

        $this->assertEquals(
            '$foo=123',
            $this->renderTokensToString($token->getTokens(new RenderingContext())),
        );
    }

    public function testAddsDefaultValueWithSingleQuotesIfString()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 'Hello World',
        );

        $this->assertEquals(
            '$foo=\'Hello World\'',
            $this->renderTokensToString($token->getTokens(new RenderingContext())),
        );
    }

    public function testAddsDefaultValueWithStringRepresentationIfBool()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: true,
        );

        $this->assertEquals(
            '$foo=true',
            $this->renderTokensToString($token->getTokens(new RenderingContext())),
        );
    }

    public function testVariadicExpansionTokenAppearsBeforeVariable()
    {
        $token = new ParameterDef(
            'reallyLongIdentifier',
            isVariadic: true,
        );

        $this->assertEquals(
            '...$reallyLongIdentifier',
            $this->renderTokensToString($token->getTokens(new RenderingContext())),
        );
    }
}
