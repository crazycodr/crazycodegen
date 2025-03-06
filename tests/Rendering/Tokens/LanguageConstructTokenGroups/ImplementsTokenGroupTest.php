<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ClassDefinitionRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ImplementsTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;
use PHPUnit\Framework\TestCase;

class ImplementsTokenGroupTest extends TestCase
{
    use RenderTokensToStringTrait;

    public function testInlineScenarioRendersImplementsKeyword()
    {
        $token = new ImplementsTokenGroup(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;

        $this->assertEquals(
            'implements \\JsonSerializable, \\ArrayAccess',
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioRendersEachImplementationUsingSingleTypeTokenGroupOrOriginal()
    {
        $token = new ImplementsTokenGroup(
            implementations: ['\\JsonSerializable', new SingleTypeTokenGroup('\\ArrayAccess')],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;

        $this->assertEquals(<<<EOS
            implements \\JsonSerializable, \\ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioAddsConfiguredSpacesBetweenImplementsAndFirstItem()
    {
        $token = new ImplementsTokenGroup(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 2;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;

        $this->assertEquals(<<<EOS
            implements  \\JsonSerializable, \\ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testInlineScenarioAddsConfiguredSpacesAfterEachImplementsExceptLastOne()
    {
        $token = new ImplementsTokenGroup(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 2;

        $this->assertEquals(<<<EOS
            implements \\JsonSerializable,  \\ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderInlineScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioRendersImplementsKeywordAndFirstItemOnSameLine()
    {
        $token = new ImplementsTokenGroup(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 2;

        $this->assertEquals(<<<EOS
            implements \\JsonSerializable,
                       \\ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }

    public function testChopDownScenarioRendersConfiguredSpacesAfterImplementsAddPadsAccordingly()
    {
        $token = new ImplementsTokenGroup(
            implementations: ['\\JsonSerializable', '\\ArrayAccess'],
        );

        $rules = new RenderingRules();
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;

        $this->assertEquals(<<<EOS
            implements    \\JsonSerializable,
                          \\ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }
}