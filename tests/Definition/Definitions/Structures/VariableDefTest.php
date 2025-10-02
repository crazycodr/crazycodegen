<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Rendering\TokenizationContext;
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
            $token->getSimpleTokens(new TokenizationContext())[0]
        );
    }

    public function testVariableNameIsRenderedNext()
    {
        $token = new VariableDef('foo');

        $this->assertEquals(
            new Token('foo'),
            $token->getSimpleTokens(new TokenizationContext())[1]
        );
    }
}
