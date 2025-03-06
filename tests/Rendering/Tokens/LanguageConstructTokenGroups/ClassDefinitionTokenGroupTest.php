<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\ClassDefinitionRules;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ClassDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Rendering\Traits\RenderTokensToStringTrait;
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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 4;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 4;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 4;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::NEVER_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 1;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 4;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 4;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 4;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 4;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 4;

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
        $rules->classes = new ClassDefinitionRules();
        $rules->classes->extendsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnNextLine = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->implementsOnDifferentLines = ChopWrapDecisionEnum::ALWAYS_CHOP_OR_WRAP;
        $rules->classes->spacesAfterImplementsKeyword = 1;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $rules->classes->classOpeningBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $rules->classes->spacesBeforeOpeningBraceIfSameLine = 4;

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