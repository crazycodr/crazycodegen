<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ClassDefinitionRenderingRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Renderers\RenderTokensToStringTrait;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ImplementsTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
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
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;

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
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;

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
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 2;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;

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
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 2;

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
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 2;

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
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 4;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;

        $this->assertEquals(<<<EOS
            implements    \\JsonSerializable,
                          \\ArrayAccess
            EOS,
            $this->renderTokensToString($token->renderChopDownScenario(new RenderContext(), $rules))
        );
    }
}