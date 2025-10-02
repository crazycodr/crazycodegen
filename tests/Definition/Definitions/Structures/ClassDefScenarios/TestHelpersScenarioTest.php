<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures\ClassDefScenarios;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Types\MultiTypeDef;
use CrazyCodeGen\Definition\Expressions\Comment;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\IssetOp;
use CrazyCodeGen\Definition\Expressions\Operations\NewOp;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Operators\Assignment\AssignOp;
use CrazyCodeGen\Definition\Expressions\Operators\Comparisons\InstanceOfOp;
use CrazyCodeGen\Definition\Expressions\Operators\LogicalOperators\NotOp;
use CrazyCodeGen\Definition\Expressions\Structures\Condition;
use CrazyCodeGen\Definition\Expressions\Structures\ConditionChain;
use CrazyCodeGen\Tests\Common\Formatters\PhpCsFixerFormatter;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class TestHelpersScenarioTest extends TestCase
{
    use TokenFunctions;
    use FlattenFunction;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testAbilityToGenerateTestHelperFromPreviousInternalFramework(): void
    {
        $testCaseType = new ClassTypeDef('PHPUnit\Framework\TestCase');
        $serviceBuilderType = new ClassTypeDef('Internal\TestFramework\MockingFramework\Builders\ServiceBuilders\InternalApi\Auditing\Services\AuditingTrackingServiceManagerBuilder');
        $configApiSpyBuilderType = new ClassTypeDef('Internal\TestFramework\MockingFramework\MockHelpers\ConfigApiClient\ConfigApiClientSpyBuilder');
        $configApiClientType = new ClassTypeDef('ConfigApi\ConfigApiClient');
        $trackingServiceManagerType = new ClassTypeDef('internal\Auditing\Services\AuditingTrackingServiceManager');
        $configApiManagerType = new ClassTypeDef('internal\managers\ConfigApiManager');

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
            ->setReturnType(new BuiltInTypeSpec(BuiltInTypesEnum::void))
            ->addInstruction(ParentContext::to(new CallOp('setUp')))
            ->addInstruction(new ConditionChain([
                new Condition(
                    condition: new NotOp(new InstanceOfOp(
                        left: $configApiManagerType->to(new CallOp('getClient')),
                        right: $configApiClientType,
                    )),
                    instructions: [
                        $configApiManagerType->to(new CallOp(subject: 'setClient', arguments: [null])),
                    ]
                )
            ]))
            ->addInstruction(new AssignOp(
                subject: ThisContext::to($configApiClientBackupProperty),
                value: $configApiManagerType->to(new CallOp('getClient')),
            ))
            ->addInstruction($configApiManagerType->to(new CallOp(subject: 'setClient', arguments: [null])))
            ->addInstruction(new AssignOp(
                subject: ThisContext::to($configApiClientSpyBuilderProperty),
                value: new NewOp(
                    class: $configApiSpyBuilderType,
                    arguments: [
                        $configApiManagerType->to(new CallOp('getClient'))
                    ],
                ),
            ));
        $tearDownMethod = (new MethodDef('tearDown'))
            ->setReturnType(new BuiltInTypeSpec(BuiltInTypesEnum::void))
            ->addInstruction(
                ThisContext::to($configApiClientSpyBuilderProperty)
                    ->to(new CallOp('getService'))
                    ->to(new CallOp(subject: 'validateMandateExpectations', arguments: [new ThisContext()]))
            )
            ->addInstruction($configApiManagerType->to(new CallOp(
                subject: 'setClient',
                arguments: [
                    ThisContext::to($configApiClientBackupProperty),
                ],
            )))
            ->addInstruction(ParentContext::to(new CallOp('tearDown')));
        $scenarioBuildingCallable = new ParameterDef(
            name: 'scenarioBuildingCallable',
            type: new MultiTypeDef([
                new BuiltInTypeSpec(BuiltInTypesEnum::null),
                new BuiltInTypeSpec(BuiltInTypesEnum::callable)
            ]),
            defaultValue: null
        );
        $getConfigApiSpyBuilderMethod = (new MethodDef('getConfigApiClientSpyBuilder'))
            ->setVisibility(VisibilityEnum::PROTECTED)
            ->addParameter($scenarioBuildingCallable)
            ->setReturnType($configApiSpyBuilderType)
            ->addInstruction(new ConditionChain([
                new Condition(
                    condition: new NotOp(new IssetOp(ThisContext::to($configApiClientSpyBuilderProperty))),
                    instructions: [
                        new AssignOp(
                            subject: ThisContext::to($configApiClientSpyBuilderProperty),
                            value: new NewOp(
                                class: $configApiSpyBuilderType,
                                arguments: [
                                    $configApiManagerType->to(new CallOp('getClient')),
                                ],
                            ),
                        ),
                    ],
                ),
            ]))
            ->addInstruction(new NewLinesToken())
            ->addInstruction(new Condition(
                condition: $scenarioBuildingCallable,
                instructions: [
                    new CallOp(
                        subject: $scenarioBuildingCallable,
                        arguments: [ThisContext::to($configApiClientSpyBuilderProperty)],
                    ),
                ],
            ))
            ->addInstruction(new NewLinesToken())
            ->addInstruction(new ReturnOp(ThisContext::to($configApiClientSpyBuilderProperty)));
        $getAuditingTrackingServiceManagerBuilderMethod = (new MethodDef('getAuditingTrackingServiceManagerBuilder'))
            ->setReturnType($serviceBuilderType)
            ->addInstruction(new NewLinesToken())
            ->addInstruction(new Comment('@noinspection PhpUnhandledExceptionInspection'))
            ->addInstruction(new ReturnOp(new NewOp(
                $serviceBuilderType,
                arguments: [
                    ThisContext::to(new CallOp('createMock', arguments: [$trackingServiceManagerType])),
                ],
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

        $code = $this->renderTokensToString($classDef->getTokens(new RenderingContext()));
        $newCode = (new PhpCsFixerFormatter())->format($code);

        $this->assertEquals(
            <<<'EOS'
            <?php
            
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
             * Do not edit this file as the content will be replaced automatically the next
             * time someone will run the generate command.
             *
             * If you want to learn more about this framework, visit the documentation on
             * Confluence at:
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
            
                protected function getConfigApiClientSpyBuilder(null|callable $scenarioBuildingCallable = null): ConfigApiClientSpyBuilder
                {
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
                    // @noinspection PhpUnhandledExceptionInspection
                    return new AuditingTrackingServiceManagerBuilder($this->createMock(AuditingTrackingServiceManager::class));
                }
            }
            
            EOS,
            $newCode,
        );
    }
}
