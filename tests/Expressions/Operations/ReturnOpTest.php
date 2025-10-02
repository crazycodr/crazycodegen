<?php

namespace CrazyCodeGen\Tests\Expressions\Operations;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ReturnOpTest extends TestCase
{
    use TokenFunctions;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testReturnTokenAndSpaceAddedBeforeInstructions()
    {
        $token = new ReturnOp(new VariableDef('foo'));

        $this->assertEquals(
            <<<'EOS'
            return $foo
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
