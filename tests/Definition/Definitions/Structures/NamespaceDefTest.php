<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NamespaceDefTest extends TestCase
{
    use TokenFunctions;

    public function testNamespaceKeywordAndPathAndSemiColonAreRendered()
    {
        $token = new NamespaceDef('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->namespaces->spacesAfterNamespace = 1;
        $rules->namespaces->newLinesAfterSemiColon = 0;

        $this->assertEquals(
            <<<'EOS'
            namespace CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenKeywordAndPathAreRespected()
    {
        $token = new NamespaceDef('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->namespaces->spacesAfterNamespace = 4;
        $rules->namespaces->newLinesAfterSemiColon = 0;

        $this->assertEquals(
            <<<'EOS'
            namespace    CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testLinesAfterNamespaceDeclarationIsRespected()
    {
        $token = new NamespaceDef('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->namespaces->spacesAfterNamespace = 1;
        $rules->namespaces->newLinesAfterSemiColon = 2;

        $this->assertEquals(
            <<<'EOS'
            namespace CrazyCodeGen\Tests;
            
            
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }
}
