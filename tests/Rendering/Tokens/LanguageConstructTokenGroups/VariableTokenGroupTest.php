<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use PHPUnit\Framework\TestCase;

class VariableTokenGroupTest extends TestCase
{
    public function testVariableIsRenderedWithLeadingDollarSign()
    {
        $token = new VariableDef('foo');

        $this->assertEquals(
            new DollarToken(),
            $token->getTokens(new RenderContext(), new RenderingRules())[0]
        );
    }

    public function testStringNameIsTransformedIntoIdentifierAndRendered()
    {
        $token = new VariableDef($name = new Token('foo'));

        $this->assertEquals(
            $name,
            $token->getTokens(new RenderContext(), new RenderingRules())[1]
        );
    }

    public function testVariableNameIsRenderedNext()
    {
        $token = new VariableDef('foo');

        $this->assertEquals(
            new Token('foo'),
            $token->getTokens(new RenderContext(), new RenderingRules())[1]
        );
    }
}
