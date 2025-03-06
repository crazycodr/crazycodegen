<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;
use PHPUnit\Framework\TestCase;

class VariableTokenGroupTest extends TestCase
{
    public function testVariableIsRenderedWithLeadingDollarSign()
    {
        $token = new VariableTokenGroup('foo');

        $this->assertEquals(
            new DollarToken(),
            $token->render(new RenderContext(), new RenderingRules())[0]
        );
    }

    public function testStringNameIsTransformedIntoIdentifierAndRendered()
    {
        $token = new VariableTokenGroup($name = new IdentifierToken('foo'));

        $this->assertEquals(
            $name,
            $token->render(new RenderContext(), new RenderingRules())[1]
        );
    }

    public function testVariableNameIsRenderedNext()
    {
        $token = new VariableTokenGroup('foo');

        $this->assertEquals(
            new IdentifierToken('foo'),
            $token->render(new RenderContext(), new RenderingRules())[1]
        );
    }
}