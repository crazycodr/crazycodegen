<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnVal;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ReturnInstructionTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testReturnTokenAndSpaceAddedBeforeInstructions()
    {
        $token = new ReturnVal(new VariableDef('foo'));

        $this->assertEquals(
            <<<'EOS'
            return $foo;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }
}
