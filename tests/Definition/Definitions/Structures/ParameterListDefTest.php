<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ParameterListDefTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testHasStartAndEndParenthesisAndTokensFromArgumentAndNoTrailingComma(): void
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
            ],
        );

        $this->assertEquals(
            '($foo)',
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testHasCommaAndSpaceAfterEachArgumentExceptLast(): void
    {
        $token = new ParameterListDef(
            [
                new ParameterDef('foo'),
                new ParameterDef('bar'),
                new ParameterDef('baz'),
            ],
        );

        $this->assertEquals(
            '($foo,$bar,$baz)',
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
