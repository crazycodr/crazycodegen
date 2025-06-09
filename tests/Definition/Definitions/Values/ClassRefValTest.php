<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ClassRefValTest extends TestCase
{
    use TokenFunctions;

    public function testTypeIsRenderedAlongWithClassSuffix()
    {
        $type = new ClassTypeDef('Foo\\Bar\\Baz');
        $token = new ClassRefVal($type);

        $this->assertEquals(
            <<<'EOS'
            Foo\Bar\Baz::class
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderContext(), new RenderingRules()))
        );
    }

    public function testTypeIsRenderedAsShortIfImportedInContext()
    {
        $type = new ClassTypeDef('Foo\\Bar\\Baz');
        $token = new ClassRefVal($type);

        $context = new RenderContext();
        $context->importedClasses[] = 'Foo\\Bar\\Baz';

        $this->assertEquals(
            <<<'EOS'
            Baz::class
            EOS,
            $this->renderTokensToString($token->getTokens($context, new RenderingRules()))
        );
    }
}
