<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures\Scenarios;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\MultiTypeDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDef;
use CrazyCodeGen\Definition\Expressions\Comment;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\ChainOp;
use CrazyCodeGen\Definition\Expressions\Operations\IssetOp;
use CrazyCodeGen\Definition\Expressions\Operations\NewOp;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Operators\Assignment\Assign;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\InstanceOfOp;
use CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators\NotOp;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class TestHelpersScenarioTest extends TestCase
{
    use TokenFunctions;

    public function testAbilityToGenerateTestHelperFromPreviousInternalFramework()
    {
        $testCaseType = new SingleTypeDef('PHPUnit\Framework\TestCase');
        $serviceBuilderType = new SingleTypeDef('Internal\TestFramework\MockingFramework\Builders\ServiceBuilders\InternalApi\Auditing\Services\AuditingTrackingServiceManagerBuilder');
        $configApiSpyBuilderType = new SingleTypeDef('Internal\TestFramework\MockingFramework\MockHelpers\ConfigApiClient\ConfigApiClientSpyBuilder');
        $configApiClientType = new SingleTypeDef('ConfigApi\ConfigApiClient');
        $trackingServiceManagerType = new SingleTypeDef('internal\Auditing\Services\AuditingTrackingServiceManager');
        $configApiManagerType = new SingleTypeDef('internal\managers\ConfigApiManager');

        $configApiClientSpyBuilderProperty = new PropertyDef(
            name: 'configApiClientSpyBuilder',
            type: $configApiSpyBuilderType,
            visibility: VisibilityEnum::PROTECTED,
        );
        $configApiClientBackupProperty = new PropertyDef(
            name: 'configApiClientBackup',
            type: $configApiClientType,
            visibility: VisibilityEnum::PROTECTED,
        );

        $setUpMethod = (new MethodDef('setUp'))
            ->setReturnType('void')
            ->addInstruction(new Instruction([
                new ChainOp([
                    new ParentContext(),
                    new CallOp('setUp'),
                ]),
            ]))
            ->addInstruction(new Condition(
                condition: new NotOp(new InstanceOfOp(
                    left: new ChainOp([$configApiManagerType, new CallOp('getClient')]),
                    right: $configApiClientType,
                )),
                trueInstructions: new ChainOp([
                    $configApiManagerType,
                    new CallOp(name: 'setClient', arguments: [null]),
                ]),
            ))
            ->addInstruction(new Assign(
                subject: new ChainOp([new ThisContext(), $configApiClientBackupProperty]),
                value: new ChainOp([
                    $configApiManagerType,
                    new CallOp('getClient'),
                ]),
            ))
            ->addInstruction(new Instruction([
                new ChainOp([
                    $configApiManagerType,
                    new CallOp(name: 'setClient', arguments: [null]),
                ]),
            ]))
            ->addInstruction(new Assign(
                subject: new ChainOp([new ThisContext(), $configApiClientSpyBuilderProperty]),
                value: new NewOp(
                    class: $configApiSpyBuilderType,
                    arguments: [
                        new ChainOp([
                            $configApiManagerType,
                            new CallOp('getClient'),
                        ]),
                    ],
                ),
            ));
        $tearDownMethod = (new MethodDef('tearDown'))
            ->setReturnType('void')
            ->addInstruction(new Instruction([
                new ChainOp([
                    new ThisContext(),
                    $configApiClientSpyBuilderProperty,
                    new CallOp('getService'),
                    new CallOp(name: 'validateMandateExpectations', arguments: [new ThisContext()]),
                ]),
            ]))
            ->addInstruction(new Instruction(new ChainOp([
                $configApiManagerType,
                new CallOp(
                    name: 'setClient',
                    arguments: [
                        new ChainOp([
                            new ThisContext(),
                            $configApiClientBackupProperty,
                        ]),
                    ],
                ),
            ])))
            ->addInstruction(new Instruction(new ChainOp([
                new ParentContext(),
                new CallOp('tearDown'),
            ])));
        $scenarioBuildingCallable = new ParameterDef(
            name: 'scenarioBuildingCallable',
            type: new MultiTypeDef(types: ['null', 'callable']),
            defaultValueIsNull: true
        );
        $getConfigApiSpyBuilderMethod = (new MethodDef('getConfigApiClientSpyBuilder'))
            ->setVisibility(VisibilityEnum::PROTECTED)
            ->addParameter($scenarioBuildingCallable)
            ->setReturnType($configApiSpyBuilderType)
            ->addInstruction(new Condition(
                condition: new NotOp(new IssetOp(new ChainOp([new ThisContext(), $configApiClientSpyBuilderProperty]))),
                trueInstructions: [
                    new Assign(
                        subject: new ChainOp([new ThisContext(), $configApiClientSpyBuilderProperty]),
                        value: new NewOp(
                            class: $configApiSpyBuilderType,
                            arguments: [
                                new ChainOp([
                                    $configApiManagerType,
                                    new CallOp('getClient'),
                                ]),
                            ],
                        ),
                    ),
                ],
            ))
            ->addInstruction(new NewLinesToken())
            ->addInstruction(new Condition(
                condition: $scenarioBuildingCallable,
                trueInstructions: new CallOp(
                    name: $scenarioBuildingCallable,
                    arguments: [new ChainOp([
                        new ThisContext(),
                        $configApiClientSpyBuilderProperty,
                    ])],
                ),
            ))
            ->addInstruction(new NewLinesToken())
            ->addInstruction(new ReturnOp(
                new ChainOp([new ThisContext(), $configApiClientSpyBuilderProperty])
            ));
        $getAuditingTrackingServiceManagerBuilderMethod = (new MethodDef('getAuditingTrackingServiceManagerBuilder'))
            ->setReturnType($serviceBuilderType)
            ->addInstruction(new Comment('@noinspection PhpUnhandledExceptionInspection', useMultiline: true))
            ->addInstruction(new ReturnOp(new NewOp(
                $serviceBuilderType,
                arguments: [
                    new ChainOp([
                        new ThisContext(),
                        new CallOp('createMock', arguments: [$trackingServiceManagerType]),
                    ]),
                ]
            )));
        $classDef = (new ClassDef('FinalizeTrackingOnRequestEndSubscriberTestHelpers'))
            ->setNamespace('Internal\Tests\Auditing\Subscribers\TestHelpers')
            ->addImport($testCaseType)
            ->addImport($serviceBuilderType)
            ->addImport($configApiSpyBuilderType)
            ->addImport($configApiClientType)
            ->addImport($trackingServiceManagerType)
            ->addImport($configApiManagerType)
            ->setDocBlock([
                'This file was generated using the Symfony command "mock-helpers:generate".',
                'Do not edit this file as the content will be replaced automatically the next time someone will run the generate command.',
                'If you want to learn more about this framework, visit the documentation on Confluence at: https://example.com/wiki/spaces/COPUNIT/pages/4051238915/Mocking+framework.',
            ])
            ->setExtends($testCaseType)
            ->addProperty($configApiClientSpyBuilderProperty)
            ->addProperty($configApiClientBackupProperty)
            ->addMethod($setUpMethod)
            ->addMethod($tearDownMethod)
            ->addMethod($getConfigApiSpyBuilderMethod)
            ->addMethod($getAuditingTrackingServiceManagerBuilderMethod);

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 125;

        $this->assertEquals(
            <<<'EOS'
            namespace Internal\Tests\Auditing\Subscribers\TestHelpers;
            
            use PHPUnit\Framework\TestCase;
            use Internal\TestFramework\MockingFramework\Builders\ServiceBuilders\InternalApi\Auditing\Services\AuditingTrackingServiceManagerBuilder;
            use Internal\TestFramework\MockingFramework\MockHelpers\ConfigApiClient\ConfigApiClientSpyBuilder;
            use ConfigApi\ConfigApiClient;
            use internal\Auditing\Services\AuditingTrackingServiceManager;
            use internal\managers\ConfigApiManager;
            
            /**
             * This file was generated using the Symfony command "mock-helpers:generate".
             *
             * Do not edit this file as the content will be replaced automatically the next time someone will run the generate command.
             *
             * If you want to learn more about this framework, visit the documentation on Confluence at:
             * https://example.com/wiki/spaces/COPUNIT/pages/4051238915/Mocking+framework.
             */
            class FinalizeTrackingOnRequestEndSubscriberTestHelpers extends TestCase
            {
                protected ConfigApiClientSpyBuilder $configApiClientSpyBuilder;
                protected ConfigApiClient $configApiClientBackup;
            
                public function setUp(): void
                {
                    parent::setUp();
                    if (!ConfigApiManager::getClient() instanceof ConfigApiClient) {
                        ConfigApiManager::setClient(null);
                    }
                    $this->configApiClientBackup = ConfigApiManager::getClient();
                    ConfigApiManager::setClient(null);
                    $this->configApiClientSpyBuilder = new ConfigApiClientSpyBuilder(ConfigApiManager::getClient());
                }
            
                public function tearDown(): void
                {
                    $this->configApiClientSpyBuilder->getService()->validateMandateExpectations($this);
                    ConfigApiManager::setClient($this->configApiClientBackup);
                    parent::tearDown();
                }
            
                protected function getConfigApiClientSpyBuilder(
                    null|callable $scenarioBuildingCallable = null,
                ): ConfigApiClientSpyBuilder {
                    if (!isset($this->configApiClientSpyBuilder)) {
                        $this->configApiClientSpyBuilder = new ConfigApiClientSpyBuilder(ConfigApiManager::getClient());
                    }
            
                    if ($scenarioBuildingCallable) {
                        $scenarioBuildingCallable($this->configApiClientSpyBuilder);
                    }
            
                    return $this->configApiClientSpyBuilder;
                }
            
                public function getAuditingTrackingServiceManagerBuilder(): AuditingTrackingServiceManagerBuilder
                {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    return new AuditingTrackingServiceManagerBuilder($this->createMock(AuditingTrackingServiceManager::class));
                }
            }
            
            EOS,
            $this->renderTokensToString($classDef->getTokens(new RenderContext(), $rules)),
        );
    }
}
