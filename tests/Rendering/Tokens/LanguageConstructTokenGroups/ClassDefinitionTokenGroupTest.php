<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListDeclarationTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ClassDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MethodDefinitionTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\PropertyTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ImportTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
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

        $rules = $this->getBaseTestingRules();

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

        $rules = $this->getBaseTestingRules();

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

        $rules = $this->getBaseTestingRules();

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

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            namespace CrazyCodeGen\Tests;
            
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testStringsAreConvertedToImportTokenGroupsAndAreRenderedWithProperLinesBetweenThemAndAfterBlock()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            imports: [
                new ImportTokenGroup('CrazyCodeGen\\Tests\\Tests1'),
                'CrazyCodeGen\\Tests\\Test2',
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesBetweenImports = 3;
        $rules->classes->newLinesAfterAllImports = 4;

        $this->assertEquals(<<<EOS
            use CrazyCodeGen\Tests\Tests1;
            
            
            use CrazyCodeGen\Tests\Test2;
            
            
            
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testImportsShortenTheTypesEvenIfSpecifiedAsStrings()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            imports: [
                new ImportTokenGroup('CrazyCodeGen\\Tests\\Test1'),
                'CrazyCodeGen\\Tests\\Test2',
            ],
            properties: [
                new PropertyTokenGroup(
                    name: 'prop1',
                    type: new SingleTypeTokenGroup(type: 'CrazyCodeGen\\Tests\\Test1'),
                ),
                new PropertyTokenGroup(
                    name: 'prop2',
                    type: new MultiTypeTokenGroup(types: ['int', 'CrazyCodeGen\\Tests\\Test2']),
                ),
            ],
            methods: [
                new MethodDefinitionTokenGroup(
                    name: 'method1',
                    arguments: new ArgumentListDeclarationTokenGroup(
                        arguments: [
                            new ArgumentDeclarationTokenGroup(
                                name: 'arg1',
                                type: 'CrazyCodeGen\\Tests\\Test2',
                            )
                        ]
                    ),
                    returnType: 'CrazyCodeGen\\Tests\\Test1',
                ),
            ]
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(<<<EOS
            use CrazyCodeGen\Tests\Test1;
            use CrazyCodeGen\Tests\Test2;
            
            class myClass
            {
                public Test1 \$prop1;
                public int|Test2 \$prop2;
            
                public function method1(Test2 \$arg1): Test1
                {
                }
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

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 60;
        $rules->classes->extendsOnNextLine = WrappingDecision::NEVER;

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

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->extendsOnNextLine = WrappingDecision::IF_TOO_LONG;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->extendsOnNextLine = WrappingDecision::ALWAYS;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 4;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 4;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->classes->spacesAfterImplementsKeyword = 4;
        $rules->classes->spacesAfterImplementCommaIfSameLine = 4;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;

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

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;

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

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;

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

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::NEVER;

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

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 90;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::IF_TOO_LONG;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;

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

        $rules = $this->getBaseTestingRules();
        $rules->classes->extendsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;

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

        $rules = $this->getBaseTestingRules();
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

        $rules = $this->getBaseTestingRules();
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

        $rules = $this->getBaseTestingRules();
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

        $rules = $this->getBaseTestingRules();
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

        $rules = $this->getBaseTestingRules();
        $rules->classes->extendsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;
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

        $rules = $this->getBaseTestingRules();
        $rules->classes->extendsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;
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

    public function testPropertiesAreRenderedWithNewLinesBetweenThemAsExpected()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            properties: [
                new PropertyTokenGroup(name: 'prop1'),
                new PropertyTokenGroup(name: 'prop2'),
                new PropertyTokenGroup(name: 'prop3'),
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesBetweenProperties = 3;

        $this->assertEquals(<<<EOS
            class myClass
            {
                public \$prop1;
            
            
                public \$prop2;
            
            
                public \$prop3;
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testMethodsAreRenderedWithNewLinesBetweenThemAsConfigured()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            methods: [
                new MethodDefinitionTokenGroup(name: 'method1'),
                new MethodDefinitionTokenGroup(name: 'method2'),
                new MethodDefinitionTokenGroup(name: 'method3'),
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesBetweenMethods = 4;

        $this->assertEquals(<<<EOS
            class myClass
            {
                public function method1()
                {
                }
            
            
            
                public function method2()
                {
                }
            
            
            
                public function method3()
                {
                }
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testLinesBetweenPropertiesAndMethodsAreRespected()
    {
        $token = new ClassDefinitionTokenGroup(
            name: 'myClass',
            properties: [
                new PropertyTokenGroup(name: 'prop1'),
            ],
            methods: [
                new MethodDefinitionTokenGroup(name: 'method1'),
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesBetweenPropertiesAndMethods = 4;

        $this->assertEquals(<<<EOS
            class myClass
            {
                public \$prop1;
            
            
            
                public function method1()
                {
                }
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    private function getBaseTestingRules(): RenderingRules
    {
        $newRules = new RenderingRules();
        $newRules->lineLength = 120;
        $newRules->argumentLists->spacesAfterArgumentComma = 1;
        $newRules->argumentLists->addTrailingCommaToLastItemInChopDown = true;
        $newRules->argumentLists->padTypeNames = true;
        $newRules->argumentLists->padIdentifiers = true;
        $newRules->classes->extendsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $newRules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $newRules->classes->implementsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $newRules->classes->spacesAfterImplementsKeyword = 1;
        $newRules->classes->spacesAfterImplementCommaIfSameLine = 1;
        $newRules->classes->classOpeningBrace = BracePositionEnum::NEXT_LINE;
        $newRules->classes->classClosingBrace = BracePositionEnum::NEXT_LINE;
        $newRules->classes->spacesBeforeOpeningBraceIfSameLine = 1;
        $newRules->classes->newLinesBetweenImports = 1;
        $newRules->classes->newLinesAfterAllImports = 2;
        $newRules->classes->newLinesBetweenProperties = 1;
        $newRules->classes->newLinesBetweenPropertiesAndMethods = 2;
        $newRules->classes->newLinesBetweenMethods = 2;
        $newRules->classes->newLinesAfterClosingBrace = 0;
        $newRules->methods->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $newRules->methods->funcOpeningBrace = BracePositionEnum::NEXT_LINE;
        $newRules->methods->funcClosingBrace = BracePositionEnum::NEXT_LINE;
        $newRules->methods->spacesBetweenAbstractAndNextToken = 1;
        $newRules->methods->spacesBetweenVisibilityAndNextToken = 1;
        $newRules->methods->spacesBetweenStaticAndNextToken = 1;
        $newRules->methods->spacesBetweenFunctionAndIdentifier = 1;
        $newRules->methods->spacesBetweenIdentifierAndArgumentList = 0;
        $newRules->methods->spacesBetweenArgumentListAndReturnColon = 0;
        $newRules->methods->spacesBetweenReturnColonAndType = 1;
        $newRules->methods->spacesBeforeOpeningBraceIfSameLine = 1;
        $newRules->properties->spacesAfterVisibility = 1;
        $newRules->properties->spacesAfterStaticKeyword = 1;
        $newRules->properties->spacesAfterType = 1;
        $newRules->properties->spacesAfterIdentifier = 1;
        $newRules->properties->spacesAfterEquals = 1;
        return $newRules;
    }
}