<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ImportTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImportTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testImportRendersUseTypeAndSemiColonWithConfiguredSpaces()
    {
        $token = new ImportTokenGroup('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->imports->spacesAfterUse = 4;

        $this->assertEquals(
            <<<'EOS'
            use    CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testAliasIsAddedWithProperSpacing()
    {
        $token = new ImportTokenGroup('CrazyCodeGen\\Tests', 'tests');

        $rules = new RenderingRules();
        $rules->imports->spacesAfterType = 4;
        $rules->imports->spacesAfterAs = 4;

        $this->assertEquals(
            <<<'EOS'
            use CrazyCodeGen\Tests    as    tests;
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}
