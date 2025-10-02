<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\TokenizationContext;
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
            $this->renderTokensToString($token->getSimpleTokens(new TokenizationContext()))
        );
    }

    public function testTypeIsRenderedAsShortIfImportedInContext()
    {
        $type = new ClassTypeDef('Foo\\Bar\\Baz');
        $token = new ClassRefVal($type);

        $context = new TokenizationContext();
        $context->importedClasses[] = 'Foo\\Bar\\Baz';

        $this->assertEquals(
            <<<'EOS'
            Baz::class
            EOS,
            $this->renderTokensToString($token->getSimpleTokens($context))
        );
    }
}
