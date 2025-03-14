<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
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

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testAddsTypeInFrontOfIdentifierAndSeparatesWithSpace()
    {
        $token = new ParameterDef(
            'foo',
            new BuiltInTypeSpec('int'),
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            'int $foo',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueAfterIdentifierWithSpaceBetweenIdentifierAndEqual()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo = 123',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueWithSingleQuotesIfString()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 'Hello World',
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo = \'Hello World\'',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueWithStringRepresentationIfBool()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: true,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo = true',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredSpacesBetweenTypeAndIdentifierAsPerRules()
    {
        $token = new ParameterDef(
            'foo',
            new BuiltInTypeSpec('int'),
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 2;
        $rules->parameters->spacesAfterIdentifier = 2;
        $rules->parameters->spacesAfterEquals = 2;

        $this->assertEquals(
            'int  $foo',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingTypeProperly()
    {
        $token = new ParameterDef(
            'foo',
            new BuiltInTypeSpec('int'),
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 2;
        $rules->parameters->spacesAfterIdentifier = 2;
        $rules->parameters->spacesAfterEquals = 2;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForTypes = 7;

        $this->assertEquals(
            'int     $foo',
            $this->renderTokensToString($token->getTokens($context, $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingTypeProperlyEvenIfThereIsNoType()
    {
        $token = new ParameterDef(
            'foo',
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 2;
        $rules->parameters->spacesAfterIdentifier = 2;
        $rules->parameters->spacesAfterEquals = 2;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForTypes = 7;

        $this->assertEquals(
            '        $foo',
            $this->renderTokensToString($token->getTokens($context, $rules)),
        );
    }

    public function testAddsTheConfiguredSpacesBetweenIdentifierAndEqualsAsPerRules()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 2;
        $rules->parameters->spacesAfterIdentifier = 2;
        $rules->parameters->spacesAfterEquals = 2;

        $this->assertEquals(
            '$foo  =  123',
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingIdentifier()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForIdentifiers = 7;

        $this->assertEquals(
            '$foo    = 123',
            $this->renderTokensToString($token->getTokens($context, $rules)),
        );
    }

    public function testWhenTypePaddingIsLessThanTypeAtLeastOneSpaceIsAdded()
    {
        $token = new ParameterDef(
            'foo',
            new ClassTypeDef('reallyLongType'),
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForIdentifiers = 3;

        $this->assertEquals(
            'reallyLongType $foo',
            $this->renderTokensToString($token->getTokens($context, $rules)),
        );
    }

    public function testWhenIdentifierPaddingIsLessThanIdentifierAtLeastOneSpaceIsAdded()
    {
        $token = new ParameterDef(
            'reallyLongIdentifier',
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForIdentifiers = 3;

        $this->assertEquals(
            '$reallyLongIdentifier = 123',
            $this->renderTokensToString($token->getTokens($context, $rules)),
        );
    }

    public function testWhenVariadicExpansionTokenAppearBeforeVariable()
    {
        $token = new ParameterDef(
            'reallyLongIdentifier',
            isVariadic: true,
        );

        $this->assertEquals(
            '...$reallyLongIdentifier',
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules())),
        );
    }

    public function testPaddingOnIdentifierTakesVariadicExpansionTokenIntoAccount()
    {
        $token = new ParameterDef(
            'foo',
            defaultValue: 123,
            isVariadic: true,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForIdentifiers = 10;

        $this->assertEquals(
            '...$foo    = 123',
            $this->renderTokensToString($token->getTokens($context, $rules)),
        );
    }
}
