<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\ChopDownPaddingContext;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ArgumentDeclarationTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testRendersNameAsExpectedWithoutSpacesAround()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo')
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

        $this->assertEquals(
            '$foo',
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testAddsTypeInFrontOfIdentifierAndSeparatesWithSpace()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            new SingleTypeTokenGroup('int'),
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

        $this->assertEquals(
            'int $foo',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueAfterIdentifierWithSpaceBetweenIdentifierAndEqual()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

        $this->assertEquals(
            '$foo = 123',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueWithSingleQuotesIfString()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            defaultValue: 'Hello World',
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

        $this->assertEquals(
            '$foo = \'Hello World\'',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsDefaultValueWithStringRepresentationIfBool()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            defaultValue: true,
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

        $this->assertEquals(
            '$foo = true',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredSpacesBetweenTypeAndIdentifierAsPerRules()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            new SingleTypeTokenGroup('int'),
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 2;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 2;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 2;

        $this->assertEquals(
            'int  $foo',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingTypeProperly()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            new SingleTypeTokenGroup('int'),
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 2;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 2;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 2;

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
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 2;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 2;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 2;

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
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 2;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 2;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 2;

        $this->assertEquals(
            '$foo  =  123',
            $this->renderTokensToString($token->render(new RenderContext(), $rules)),
        );
    }

    public function testAddsTheConfiguredChopDownSpacesByPaddingIdentifier()
    {
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

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
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('foo'),
            new SingleTypeTokenGroup('reallyLongType'),
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

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
        $token = new ArgumentDeclarationTokenGroup(
            new IdentifierToken('reallyLongIdentifier'),
            defaultValue: 123,
        );

        $rules = new RenderingRules();
        $rules->arguments->spacesBetweenArgumentTypeAndIdentifier = 1;
        $rules->arguments->spacesBetweenArgumentIdentifierAndEquals = 1;
        $rules->arguments->spacesBetweenArgumentEqualsAndValue = 1;

        $context = new RenderContext();
        $context->chopDown = new ChopDownPaddingContext();
        $context->chopDown->paddingSpacesForIdentifiers = 3;

        $this->assertEquals(
            '$reallyLongIdentifier = 123',
            $this->renderTokensToString($token->render($context, $rules)),
        );
    }
}