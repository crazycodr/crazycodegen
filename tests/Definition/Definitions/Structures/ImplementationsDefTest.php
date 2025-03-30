<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ImplementationsDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\ClassRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImplementationsDefTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioRendersImplementsKeyword()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $rules->classes->spacesAfterImplements = 1;
        $rules->classes->spacesAfterImplementSeparator = 1;

        $this->assertEquals(
            'implements \\JsonSerializable, \\ArrayAccess',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioRendersEachImplementationUsingSingleTypeTokenGroupOrOriginal()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', new ClassTypeDef('\\ArrayAccess')],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $rules->classes->spacesAfterImplements = 1;
        $rules->classes->spacesAfterImplementSeparator = 1;

        $this->assertEquals(
            <<<'EOS'
            implements \JsonSerializable, \ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioAddsConfiguredSpacesBetweenImplementsAndFirstItem()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $rules->classes->spacesAfterImplements = 2;
        $rules->classes->spacesAfterImplementSeparator = 1;

        $this->assertEquals(
            <<<'EOS'
            implements  \JsonSerializable, \ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioAddsConfiguredSpacesAfterEachImplementsExceptLastOne()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $rules->classes->spacesAfterImplements = 1;
        $rules->classes->spacesAfterImplementSeparator = 2;

        $this->assertEquals(
            <<<'EOS'
            implements \JsonSerializable,  \ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioRendersImplementsKeywordAndFirstItemOnSameLine()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $rules->classes->spacesAfterImplements = 1;
        $rules->classes->spacesAfterImplementSeparator = 2;

        $this->assertEquals(
            <<<'EOS'
            implements \JsonSerializable,
                       \ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioRendersConfiguredSpacesAfterImplementsAddPadsAccordingly()
    {
        $token = new ImplementationsDef(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $rules->classes->spacesAfterImplements = 4;
        $rules->classes->spacesAfterImplementSeparator = 1;

        $this->assertEquals(
            <<<'EOS'
            implements    \JsonSerializable,
                          \ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }
}
