<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParameterDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testRendersNameAsExpectedWithoutSpacesAround(): void
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
    public function testAddsTypeInFrontOfIdentifierAndSeparatesWithSpace(): void
    {
        $token = new ParameterDef(
            'foo',
            BuiltInTypeSpec::intType(),
        );

        $this->assertEquals(
            'int $foo',
            $this->renderTokensToString($token->getTokens(new RenderingContext())),
        );
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testAddsDefaultValueAfterIdentifier(): void
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

    public function testAddsDefaultValueWithSingleQuotesIfString(): void
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

    public function testAddsDefaultValueWithStringRepresentationIfBool(): void
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

    public function testVariadicExpansionTokenAppearsBeforeVariable(): void
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
