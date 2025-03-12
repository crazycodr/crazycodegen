<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImportDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testImportRendersUseTypeAndSemiColonWithConfiguredSpaces()
    {
        $token = new ImportDef('CrazyCodeGen\\Tests');

        $rules = new RenderingRules();
        $rules->imports->spacesAfterUse = 4;

        $this->assertEquals(
            <<<'EOS'
            use    CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }

    public function testAliasIsAddedWithProperSpacing()
    {
        $token = new ImportDef('CrazyCodeGen\\Tests', 'tests');

        $rules = new RenderingRules();
        $rules->imports->spacesAfterType = 4;
        $rules->imports->spacesAfterAs = 4;

        $this->assertEquals(
            <<<'EOS'
            use CrazyCodeGen\Tests    as    tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), $rules))
        );
    }
}
