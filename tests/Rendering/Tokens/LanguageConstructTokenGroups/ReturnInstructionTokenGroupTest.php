<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ReturnInstructionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\VariableTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ReturnInstructionTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testReturnTokenAndSpaceAddedBeforeInstructions()
    {
        $token = new ReturnInstructionTokenGroup(new VariableTokenGroup('foo'));

        $this->assertEquals(
            <<<EOS
            return \$foo;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), new RenderingRules()))
        );
    }
}
