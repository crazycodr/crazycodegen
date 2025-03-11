<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ParameterDefinition;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParameterDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testRendersNameAsExpectedWithoutSpacesAround()
    {
        $token = new ParameterDefinition(
            new Token('foo')
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo',
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testAddsTypeInFrontOfIdentifierAndSeparatesWithSpace()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            new SingleTypeDefinition('int'),
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            'int $foo',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueAfterIdentifierWithSpaceBetweenIdentifierAndEqual()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo = 123',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueWithSingleQuotesIfString()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            defaultValue: 'Hello World',
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo = \'Hello World\'',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueWithStringRepresentationIfBool()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            defaultValue: true,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 1;
        $rules->parameters->spacesAfterIdentifier = 1;
        $rules->parameters->spacesAfterEquals = 1;

        $this->assertEquals(
            '$foo = true',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredSpacesBetweenTypeAndIdentifierAsPerRules()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            new SingleTypeDefinition('int'),
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 2;
        $rules->parameters->spacesAfterIdentifier = 2;
        $rules->parameters->spacesAfterEquals = 2;

        $this->assertEquals(
            'int  $foo',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingTypeProperly()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            new SingleTypeDefinition('int'),
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
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingTypeProperlyEvenIfThereIsNoType()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
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
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }

    public function testAddsTheConfiguredSpacesBetweenIdentifierAndEqualsAsPerRules()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->parameters->spacesAfterType = 2;
        $rules->parameters->spacesAfterIdentifier = 2;
        $rules->parameters->spacesAfterEquals = 2;

        $this->assertEquals(
            '$foo  =  123',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingIdentifier()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
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
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }

    public function testWhenTypePaddingIsLessThanTypeAtLeastOneSpaceIsAdded()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
            new SingleTypeDefinition('reallyLongType'),
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
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }

    public function testWhenIdentifierPaddingIsLessThanIdentifierAtLeastOneSpaceIsAdded()
    {
        $token = new ParameterDefinition(
            new Token('reallyLongIdentifier'),
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
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }

    public function testWhenVariadicExpansionTokenAppearBeforeVariable()
    {
        $token = new ParameterDefinition(
            new Token('reallyLongIdentifier'),
            isVariadic: true,
        );

        $this->assertEquals(
            '...$reallyLongIdentifier',
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules())),
        );
    }

    public function testPaddingOnIdentifierTakesVariadicExpansionTokenIntoAccount()
    {
        $token = new ParameterDefinition(
            new Token('foo'),
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
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }
}
