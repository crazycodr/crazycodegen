<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\DollarToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use PHPUnit\Framework\TestCase;

class VariableDefTest extends TestCase
{
    public function testVariableIsRenderedWithLeadingDollarSign(): void
    {
        $token = new VariableDef('foo');

        $this->assertEquals(
            new DollarToken(),
            $token->getTokens(new RenderingContext())[0]
        );
    }

    public function testVariableNameIsRenderedNext(): void
    {
        $token = new VariableDef('foo');

        $this->assertEquals(
            new Token('foo'),
            $token->getTokens(new RenderingContext())[1]
        );
    }
}
