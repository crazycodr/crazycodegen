<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ImplementsDefinition;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDefinition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\ClassRules;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ImplementsDefinitionTest extends TestCase
{
    use TokenFunctions;

    public function testInlineScenarioRendersImplementsKeyword()
    {
        $token = new ImplementsDefinition(
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
        $token = new ImplementsDefinition(
            implementations: ['\\JsonSerializable', new SingleTypeDefinition('\\ArrayAccess')],
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
        $token = new ImplementsDefinition(
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
        $token = new ImplementsDefinition(
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
        $token = new ImplementsDefinition(
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
        $token = new ImplementsDefinition(
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
