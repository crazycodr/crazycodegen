<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ClassRefValTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    public function testTypeIsRenderedAlongWithClassSuffix(): void
    {
        $type = new ClassTypeDef('Foo\\Bar\\Baz');
        $token = new ClassRefVal($type);

        $this->assertEquals(
            <<<'EOS'
            Foo\Bar\Baz::class
            EOS,
            $this->renderTokensToString($token->getTokens(new RenderingContext()))
        );
    }

    public function testTypeIsRenderedAsShortIfImportedInContext(): void
    {
        $type = new ClassTypeDef('Foo\\Bar\\Baz');
        $token = new ClassRefVal($type);

        $context = new RenderingContext();
        $context->importedClasses[] = 'Foo\\Bar\\Baz';

        $this->assertEquals(
            <<<'EOS'
            Baz::class
            EOS,
            $this->renderTokensToString($token->getTokens($context))
        );
    }
}
