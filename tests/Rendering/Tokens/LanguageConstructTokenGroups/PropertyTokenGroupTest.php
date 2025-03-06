<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\PropertyTokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;
use PHPUnit\Framework\TestCase;

class PropertyTokenGroupTest extends TestCase
{
    use RenderTokensToStringTrait;

    public function testRendersVisibilityAndNameWithSpaces()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterVisibility = 4;

        $this->assertEquals(<<<EOS
            public    \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRendersNameFromStringAsExpected()
    {
        $token = new PropertyTokenGroup(
            name: 'foo',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            public \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDifferentVisibilityPropertyRendered()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            visibility: VisibilityEnum::PROTECTED,
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            protected \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRendersStaticModifierWithSpaces()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            static: true
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterStaticKeyword = 4;

        $this->assertEquals(<<<EOS
            public static    \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRendersTypeWithSpaces()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            type: 'int'
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterType = 4;

        $this->assertEquals(<<<EOS
            public int    \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testRendersComplexTypeAsExpected()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            type: new MultiTypeTokenGroup(types: ['int', 'string', 'bool'])
        );

        $rules = $this->getTestRules();

        $this->assertEquals(<<<EOS
            public int|string|bool \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDefaultValueRendersAfterNameWithExpectedSpaces()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            defaultValue: 'Hello world'
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterIdentifier = 4;
        $rules->properties->spacesAfterEquals = 4;

        $this->assertEquals(<<<EOS
            public \$foo    =    'Hello world';
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testContextPaddingIsRespectedOverRules()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            type: 'int',
            visibility: VisibilityEnum::PROTECTED,
            static: true,
            defaultValue: 'Hello world',
        );

        $rules = $this->getTestRules();
        $context = new RenderContext();
        $context->chopDown->paddingSpacesForVisibilities = 9;
        $context->chopDown->paddingSpacesForModifiers = 10;
        $context->chopDown->paddingSpacesForTypes = 11;
        $context->chopDown->paddingSpacesForIdentifiers = 12;

        $this->assertEquals(<<<EOS
            protected static    int        \$foo        = 'Hello world';
            EOS,
            $this->renderTokensToString($token->render($context, $rules))
        );
    }

    public function testContextPaddingRendersAtLeastOneSpaceEvenIfSmallerToNotCreateInvalidCode()
    {
        $token = new PropertyTokenGroup(
            name: new IdentifierToken('foo'),
            type: 'int',
            visibility: VisibilityEnum::PROTECTED,
            static: true,
            defaultValue: 'Hello world',
        );

        $rules = $this->getTestRules();
        $context = new RenderContext();
        $context->chopDown->paddingSpacesForVisibilities = 3;
        $context->chopDown->paddingSpacesForModifiers = 3;
        $context->chopDown->paddingSpacesForTypes = 3;
        $context->chopDown->paddingSpacesForIdentifiers = 3;

        $this->assertEquals(<<<EOS
            protected static int \$foo = 'Hello world';
            EOS,
            $this->renderTokensToString($token->render($context, $rules))
        );
    }

    private function getTestRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->properties->spacesAfterVisibility = 1;
        $rules->properties->spacesAfterStaticKeyword = 1;
        $rules->properties->spacesAfterType = 1;
        $rules->properties->spacesAfterIdentifier = 1;
        $rules->properties->spacesAfterEquals = 1;
        return $rules;
    }
}