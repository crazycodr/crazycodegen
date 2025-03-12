<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;
use CrazyCodeGen\Definition\Definitions\Structures\MultiTypeDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class PropertyDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testRendersVisibilityAndNameWithSpaces()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterVisibility = 4;

        $this->assertEquals(
            <<<'EOS'
            public    $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    private function getTestRules(): RenderingRules
    {
        $rules = new RenderingRules();
        $rules->properties->spacesAfterVisibility = 1;
        $rules->properties->spacesAfterStatic = 1;
        $rules->properties->spacesAfterType = 1;
        $rules->properties->spacesAfterIdentifier = 1;
        $rules->properties->spacesAfterEquals = 1;
        return $rules;
    }

    public function testRendersNameFromStringAsExpected()
    {
        $token = new PropertyDef(
            name: 'foo',
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            public $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testDifferentVisibilityPropertyRendered()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
            visibility: VisibilityEnum::PROTECTED,
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            protected $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRendersStaticModifierWithSpaces()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
            static: true
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterStatic = 4;

        $this->assertEquals(
            <<<'EOS'
            public static    $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRendersTypeWithSpaces()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
            type: 'int'
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterType = 4;

        $this->assertEquals(
            <<<'EOS'
            public int    $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testRendersComplexTypeAsExpected()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
            type: new MultiTypeDef(types: ['int', 'string', 'bool'])
        );

        $rules = $this->getTestRules();

        $this->assertEquals(
            <<<'EOS'
            public int|string|bool $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testDefaultValueRendersAfterNameWithExpectedSpaces()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
            defaultValue: 'Hello world'
        );

        $rules = $this->getTestRules();
        $rules->properties->spacesAfterIdentifier = 4;
        $rules->properties->spacesAfterEquals = 4;

        $this->assertEquals(
            <<<'EOS'
            public $foo    =    'Hello world';
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testContextPaddingIsRespectedOverRules()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
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

        $this->assertEquals(
            <<<'EOS'
            protected static    int        $foo        = 'Hello world';
            EOS,
            $this->renderTokensToString($token->getTokens($context, $rules))
        );
    }

    public function testContextPaddingRendersAtLeastOneSpaceEvenIfSmallerToNotCreateInvalidCode()
    {
        $token = new PropertyDef(
            name: new Token('foo'),
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

        $this->assertEquals(
            <<<'EOS'
            protected static int $foo = 'Hello world';
            EOS,
            $this->renderTokensToString($token->getTokens($context, $rules))
        );
    }

    public function testDocBlockIsProperlyRendered()
    {
        $token = new PropertyDef(
            name: 'prop1',
            docBlock: new DocBlockDef(['This is a docblock that should be wrapped and displayed before the prop.']),
        );

        $rules = $this->getTestRules();
        $rules->docBlocks->lineLength = 40;
        $rules->properties->newLinesAfterDocBlock = 3;

        $this->assertEquals(
            <<<'EOS'
            /**
             * This is a docblock that should be
             * wrapped and displayed before the prop.
             */
            
            
            public $prop1;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }
}
