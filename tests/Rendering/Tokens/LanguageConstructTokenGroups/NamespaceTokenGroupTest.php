<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class NamespaceTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testNamespaceKeywordAndPathAndSemiColonAreRendered()
    {
        $token = new NamespaceTokenGroup('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->namespaces->spacesAfterNamespace = 1;
        $rules->namespaces->newLinesAfterSemiColon = 0;

        $this->assertEquals(<<<EOS
            namespace CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenKeywordAndPathAreRespected()
    {
        $token = new NamespaceTokenGroup('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->namespaces->spacesAfterNamespace = 4;
        $rules->namespaces->newLinesAfterSemiColon = 0;

        $this->assertEquals(<<<EOS
            namespace    CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testLinesAfterNamespaceDeclarationIsRespected()
    {
        $token = new NamespaceTokenGroup('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->namespaces->spacesAfterNamespace = 1;
        $rules->namespaces->newLinesAfterSemiColon = 2;

        $this->assertEquals(<<<EOS
            namespace CrazyCodeGen\Tests;
            
            
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}