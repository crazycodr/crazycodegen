<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImportDefTest extends TestCase
{
    use TokenFunctions;

    public function testImportRendersAsExpected()
    {
        $token = new ImportDef('CrazyCodeGen\\Tests');

        $this->assertEquals(
            <<<'EOS'
            use CrazyCodeGen\Tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testAliasIsAddedWithProperSpacing()
    {
        $token = new ImportDef('CrazyCodeGen\\Tests', 'tests');

        $this->assertEquals(
            <<<'EOS'
            use CrazyCodeGen\Tests as tests;
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }
}
