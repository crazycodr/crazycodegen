<?php

namespace CrazyCodeGen\Tests\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentListTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ArgumentTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ClassTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\DocBlockTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\ImportTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MethodTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\MultiTypeTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\PropertyTokenGroup;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\SingleTypeTokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ClassTokenGroupTest extends TestCase
{
    use TokenFunctions;

    public function testFunctionKeywordIsRendered()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<EOS
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    private function getBaseTestingRules(): RenderingRules
    {
        $newRules = new RenderingRules();
        $newRules->lineLength = 120;
        $newRules->docBlocks->lineLength = 80;
        $newRules->argumentLists->spacesAfterSeparator = 1;
        $newRules->argumentLists->addSeparatorToLastItem = true;
        $newRules->argumentLists->padTypes = true;
        $newRules->argumentLists->padIdentifiers = true;
        $newRules->classes->extendsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $newRules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;
        $newRules->classes->implementsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $newRules->classes->spacesAfterImplements = 1;
        $newRules->classes->spacesAfterImplementSeparator = 1;
        $newRules->classes->openingBrace = BracePositionEnum::DIFF_LINE;
        $newRules->classes->closingBrace = BracePositionEnum::DIFF_LINE;
        $newRules->classes->spacesBeforeOpeningBrace = 1;
        $newRules->classes->newLinesAfterDocBlock = 1;
        $newRules->classes->newLinesAfterEachImport = 1;
        $newRules->classes->newLinesAfterAllImports = 2;
        $newRules->classes->newLinesAfterEachProperty = 1;
        $newRules->classes->newLinesAfterProperties = 2;
        $newRules->classes->newLinesAfterEachMethod = 2;
        $newRules->classes->newLinesAfterClosingBrace = 0;
        $newRules->methods->argumentsOnDifferentLines = WrappingDecision::IF_TOO_LONG;
        $newRules->methods->openingBrace = BracePositionEnum::DIFF_LINE;
        $newRules->methods->closingBrace = BracePositionEnum::DIFF_LINE;
        $newRules->methods->spacesAfterAbstract = 1;
        $newRules->methods->spacesAfterVisibility = 1;
        $newRules->methods->spacesAfterStatic = 1;
        $newRules->methods->spacesAfterFunction = 1;
        $newRules->methods->spacesAfterIdentifier = 0;
        $newRules->methods->spacesAfterArguments = 0;
        $newRules->methods->spacesAfterReturnColon = 1;
        $newRules->methods->spacesBeforeOpeningBrace = 1;
        $newRules->properties->spacesAfterVisibility = 1;
        $newRules->properties->spacesAfterStatic = 1;
        $newRules->properties->spacesAfterType = 1;
        $newRules->properties->spacesAfterIdentifier = 1;
        $newRules->properties->spacesAfterEquals = 1;
        return $newRules;
    }

    public function testClassNameIsRendered()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<EOS
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testAbstractKeywordIsRendered()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            abstract: true,
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<EOS
            abstract class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testNamespaceIsRendered()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            namespace: new NamespaceTokenGroup('CrazyCodeGen\\Tests'),
        );

        $rules = $this->getBaseTestingRules();

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            imports: [
                new ImportTokenGroup('CrazyCodeGen\\Tests\\Tests1'),
                'CrazyCodeGen\\Tests\\Test2',
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesAfterEachImport = 3;
        $rules->classes->newLinesAfterAllImports = 4;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
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
                new MethodTokenGroup(
                    name: 'method1',
                    arguments: new ArgumentListTokenGroup(
                        arguments: [
                            new ArgumentTokenGroup(
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

        $this->assertEquals(
            <<<EOS
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

    public function testDocBlockIsProperlyRendered()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            docBlock: new DocBlockTokenGroup(['This is a docblock that should be wrapped and displayed before the class declaration.']),
        );

        $rules = $this->getBaseTestingRules();
        $rules->docBlocks->lineLength = 40;
        $rules->classes->newLinesAfterDocBlock = 3;

        $this->assertEquals(
            <<<EOS
            /**
             * This is a docblock that should be
             * wrapped and displayed before the class
             * declaration.
             */
            
            
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testExtendsIsRenderedInlineEvenIfTooLong()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
        );

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 60;
        $rules->classes->extendsOnNextLine = WrappingDecision::NEVER;

        $this->assertEquals(
            <<<EOS
            class myClass extends CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testExtendsIsRenderedOnNewLineWhenTooLongAndExtendsIsTabbedIn()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
        );

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->extendsOnNextLine = WrappingDecision::IF_TOO_LONG;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests',
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->extendsOnNextLine = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;
        $rules->classes->spacesAfterImplements = 4;
        $rules->classes->spacesAfterImplementSeparator = 4;

        $this->assertEquals(
            <<<EOS
            class myClass implements    CrazyCodeGen\Tests
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenImplementTypesOnSameLineAreRespected()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests1',
                'CrazyCodeGen\\Tests2',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;
        $rules->classes->spacesAfterImplements = 4;
        $rules->classes->spacesAfterImplementSeparator = 4;

        $this->assertEquals(
            <<<EOS
            class myClass implements    CrazyCodeGen\Tests1,    CrazyCodeGen\Tests2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSpacesBetweenImplementsAndTypesAreTakenIntoPaddingAccountWhenOnMultipleLines()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests1',
                'CrazyCodeGen\\Tests2',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->classes->spacesAfterImplements = 4;
        $rules->classes->spacesAfterImplementSeparator = 4;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;

        $this->assertEquals(
            <<<EOS
            class myClass implements CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testImplementsIsRenderedOnDiffLineIfTooLong()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\LongNamespace\\OfAClass\\ThatDoesNotExist\\AndExplodesCharLimit',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->implementsOnNextLine = WrappingDecision::IF_TOO_LONG;

        $this->assertEquals(
            <<<EOS
            class myClass
                implements CrazyCodeGen\Tests\LongNamespace\OfAClass\ThatDoesNotExist\AndExplodesCharLimit
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testImplementsIsRenderedOnDiffLineEvenIfNotLongEnough()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->lineLength = 100;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
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

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
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

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<EOS
            class myClass
                implements CrazyCodeGen\Tests\Test1,
                           CrazyCodeGen\Tests\Test2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testMultipleImplementsAreNotRenderedOnIndividualLinesWhenForcedToBecauseImplementsOnDiffLineIsPrevented()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            implements: [
                'CrazyCodeGen\\Tests\\Test1',
                'CrazyCodeGen\\Tests\\Test2',
            ],
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->implementsOnNextLine = WrappingDecision::NEVER;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;

        $this->assertEquals(
            <<<EOS
            class myClass implements CrazyCodeGen\Tests\Test1, CrazyCodeGen\Tests\Test2
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testCombinationOfExtendsAndImplementsWithForcedWrappingAllGoOnDifferentLines()
    {
        $token = new ClassTokenGroup(
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

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->closingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<EOS
            class myClass    {}
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningAndDiffLineClosingBraceAreAfterClassNameAndOnDiffLineWithExpectedSpaces()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->classes->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<EOS
            class myClass    {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDiffLineOpeningAndSameLineClosingBraceAreUnderClassNameAndNoExtraSpacesAreAdded()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->classes->closingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<EOS
            class myClass
            {}
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testDiffLineOpeningAndDiffLineClosingBraceAreUnderClassNameAndNoExtraSpacesAreAdded()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->openingBrace = BracePositionEnum::DIFF_LINE;
        $rules->classes->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->classes->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<EOS
            class myClass
            {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningBraceEndsUpAfterExtendsWithProperSpacing()
    {
        $token = new ClassTokenGroup(
            name: 'myClass',
            extends: 'CrazyCodeGen\\Tests\\Test1',
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->extendsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnNextLine = WrappingDecision::ALWAYS;
        $rules->classes->implementsOnDifferentLines = WrappingDecision::ALWAYS;
        $rules->classes->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->classes->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<EOS
            class myClass
                extends CrazyCodeGen\Tests\Test1    {
            }
            EOS,
            $this->renderTokensToString($token->render(new RenderContext(), $rules))
        );
    }

    public function testSameLineOpeningBraceEndsUpAfterImplementsWithProperSpacing()
    {
        $token = new ClassTokenGroup(
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
        $rules->classes->openingBrace = BracePositionEnum::SAME_LINE;
        $rules->classes->closingBrace = BracePositionEnum::DIFF_LINE;
        $rules->classes->spacesBeforeOpeningBrace = 4;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            properties: [
                new PropertyTokenGroup(name: 'prop1'),
                new PropertyTokenGroup(name: 'prop2'),
                new PropertyTokenGroup(name: 'prop3'),
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesAfterEachProperty = 3;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            methods: [
                new MethodTokenGroup(name: 'method1'),
                new MethodTokenGroup(name: 'method2'),
                new MethodTokenGroup(name: 'method3'),
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesAfterEachMethod = 4;

        $this->assertEquals(
            <<<EOS
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
        $token = new ClassTokenGroup(
            name: 'myClass',
            properties: [
                new PropertyTokenGroup(name: 'prop1'),
            ],
            methods: [
                new MethodTokenGroup(name: 'method1'),
            ]
        );

        $rules = $this->getBaseTestingRules();
        $rules->classes->newLinesAfterProperties = 4;

        $this->assertEquals(
            <<<EOS
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
}
