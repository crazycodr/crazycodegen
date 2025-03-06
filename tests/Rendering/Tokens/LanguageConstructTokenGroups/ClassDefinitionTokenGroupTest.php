<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ClassDefinitionRenderingRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Renderers\RenderTokensToStringTrait;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ClassDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use PHPUnit\Framework\TestCase;

class ClassDefinitionTokenGroupTest extends TestCase
{
    use RenderTokensToStringTrait;

    public function testFunctionKeywordIsRendered()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testClassNameIsRendered()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testAbstractKeywordIsRendered()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            abstract: true,
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            abstract class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testNamespaceIsRendered()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            namespace: new NamespaceTokenGroup('CrazyCodeGen\\Tests'),
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            namespace CrazyCodeGen\Tests;
            
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testExtendsIsRenderedInlineEvenIfTooLong()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 60;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass extends CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testExtendsIsRenderedOnNewLineWhenTooLongAndExtendsIsTabbedIn()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 100;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                extends CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testExtendsIsRenderedOnNewLineWhenForcedToWrapAndIsTabbedInEvenIfNotLongEnough()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                extends CrazyCodeGen\Tests
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenImplementsAndFirstTypeIsRespected()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 4;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 4;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass implements    CrazyCodeGen\Tests
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenImplementTypesOnSameLineAreRespected()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests1',
                'CrazyCodeGen\\Tests2',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 4;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 4;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass implements    CrazyCodeGen\Tests1,    CrazyCodeGen\Tests2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenImplementsAndTypesAreTakenIntoPaddingAccountWhenOnMultipleLines()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests1',
                'CrazyCodeGen\\Tests2',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 4;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 4;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                implements    CrazyCodeGen\Tests1,
                              CrazyCodeGen\Tests2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testImplementsIsRenderedOnSameLineEvenIfTooLong()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass implements CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testImplementsIsRenderedOnNextLineIfTooLong()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 100;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                implements CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testImplementsIsRenderedOnNextLineEvenIfNotLongEnough()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 100;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                implements CrazyCodeGen\Tests
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testMultipleImplementsAreRenderedOnSameLineEvenIfTooLong()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
                'CrazyCodeGen\\Tests\\Test3',
                'CrazyCodeGen\\Tests\\Test4',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 100;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                implements CrazyCodeGen\Tests\Test1, CrazyCodeGen\Tests\Test2, CrazyCodeGen\Tests\Test3, CrazyCodeGen\Tests\Test4
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testMultipleImplementsAreRenderedOnIndividualLinesIfTheyAreTooLongInTotalAndAreProperlyIndented()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
                'CrazyCodeGen\\Tests\\Test3',
                'CrazyCodeGen\\Tests\\Test4',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 90;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                implements CrazyCodeGen\Tests\Test1,
                           CrazyCodeGen\Tests\Test2,
                           CrazyCodeGen\Tests\Test3,
                           CrazyCodeGen\Tests\Test4
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testMultipleImplementsAreRenderedOnIndividualLinesWhenForcedToEvenIfNotLongEnough()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                implements CrazyCodeGen\Tests\Test1,
                           CrazyCodeGen\Tests\Test2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testMultipleImplementsAreNotRenderedOnIndividualLinesWhenForcedToBecauseImplementsOnNextLineIsPrevented()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass implements CrazyCodeGen\Tests\Test1, CrazyCodeGen\Tests\Test2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testCombinationOfExtendsAndImplementsWithForcedWrappingAllGoOnDifferentLines()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\Test1',
            implements: [
                'CrazyCodeGen\\Tests\\Test2',
                'CrazyCodeGen\\Tests\\Test3',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 1;

        $this->assertEquals(<<<EOS
            class myClass
                extends CrazyCodeGen\Tests\Test1
                implements CrazyCodeGen\Tests\Test2,
                           CrazyCodeGen\Tests\Test3
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningAndClosingBraceAreAfterClassNameWithExpectedSpaces()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 4;

        $this->assertEquals(<<<EOS
            class myClass    {}
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningAndNextLineClosingBraceAreAfterClassNameAndOnNextLineWithExpectedSpaces()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 4;

        $this->assertEquals(<<<EOS
            class myClass    {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testNextLineOpeningAndSameLineClosingBraceAreUnderClassNameAndNoExtraSpacesAreAdded()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 4;

        $this->assertEquals(<<<EOS
            class myClass
            {}
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testNextLineOpeningAndNextLineClosingBraceAreUnderClassNameAndNoExtraSpacesAreAdded()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 4;

        $this->assertEquals(<<<EOS
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningBraceEndsUpAfterExtendsWithProperSpacing()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\Test1',
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 4;

        $this->assertEquals(<<<EOS
            class myClass
                extends CrazyCodeGen\Tests\Test1    {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningBraceEndsUpAfterImplementsWithProperSpacing()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
            ],
        );

        $rules = new RenderingRules();
        $rules->lineLength = 120;
        $rules->classDefinitionRenderingRules = new ClassDefinitionRenderingRules();
        $rules->classDefinitionRenderingRules->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classDefinitionRenderingRules->spacesAfterImplementsKeyword = 1;
        $rules->classDefinitionRenderingRules->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classDefinitionRenderingRules->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classDefinitionRenderingRules->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classDefinitionRenderingRules->spacesBeforeOpeningBraceIfSameLine = 4;

        $this->assertEquals(<<<EOS
            class myClass
                implements CrazyCodeGen\Tests\Test1,
                           CrazyCodeGen\Tests\Test2    {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }
}