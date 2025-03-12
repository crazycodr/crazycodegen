<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ReturnOpTest extends TestCase
{
    use TokenFunctions;

    public function testReturnTokenAndSpaceAddedBeforeInstructions()
    {
        $token = new ReturnOp(new VariableDef('foo'));

        $this->assertEquals(
            <<<'EOS'
            return $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
