<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use PHPUnit\Framework\TestCase;

class VariableDefTest extends TestCase
{
    public function testVariableIsRenderedWithLeadingDollarSign()
    {
        $token = new VariableDef('foo');

        $this->assertEquals(
            new DollarToken(),
            $token->getTokens(new RenderContext(), new RenderingRules())[0]
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
