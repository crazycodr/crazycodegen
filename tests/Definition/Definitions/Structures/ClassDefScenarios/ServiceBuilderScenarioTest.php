<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures\ClassDefScenarios;

use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Types\MultiTypeDef;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Operators\Assignment\AssignOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ServiceBuilderScenarioTest extends TestCase
{
    use TokenFunctions;

    public function testAbilityToGenerateTestBuilderClassFromPreviousInternalFramework()
    {
        $baseMockBuilderType = new ClassTypeDef('Internal\TestFramework\MockingFramework\Builders\BaseMockBuilder');
        $mockObjectType = new ClassTypeDef('PHPUnit\Framework\MockObject\MockObject');
        $hookBasketAdapterType = new ClassTypeDef('internal\Baskets\Adapters\HookBasketAdapter');
        $mockedHookBasketAdapterType = new MultiTypeDef(
            types: [$hookBasketAdapterType, $mockObjectType],
            unionTypes: false,
        );

        $mockProperty = (new PropertyDef('mock'));
        $mockParameter = (new ParameterDef('mock', $mockedHookBasketAdapterType));

        $constructor = (new MethodDef('__construct'))
            ->addParameter($mockParameter)
            ->addInstruction(new AssignOp(
                subject: ThisContext::to($mockProperty),
                value: $mockParameter,
            ));
        $getMockedClassesMethod = (new MethodDef('getMockedClasses'))
            ->setReturnType('array')
            ->setStatic(true)
            ->addInstruction(new ReturnOp([$hookBasketAdapterType]));
        $getServiceMethod = (new MethodDef('getService'))
            ->setReturnType($mockedHookBasketAdapterType)
            ->addInstruction(new ReturnOp(ThisContext::to($mockProperty)));
        $classDef = (new ClassDef('HookBasketAdapterBuilder'))
            ->setNamespace('Internal\TestFramework\MockingFramework\Builders\ServiceBuilders\InternalApi\Baskets\Adapters')
            ->addImport($mockObjectType)
            ->addImport($baseMockBuilderType)
            ->addImport($hookBasketAdapterType)
            ->setDocBlock([
                'This file was generated using the Symfony command "mock-helpers:generate".',
                'Do not edit this file as the content will be replaced automatically the next time someone will run the generate command.',
                'If you want to learn more about this framework, visit the documentation on Confluence at: https://example.com/wiki/spaces/COPUNIT/pages/4051238915/Mocking+framework.',
            ])
            ->setExtends($baseMockBuilderType)
            ->addMethod($constructor)
            ->addMethod($getMockedClassesMethod)
            ->addMethod($getServiceMethod);

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 125;

        $this->assertEquals(
            <<<'EOS'
            namespace Internal\TestFramework\MockingFramework\Builders\ServiceBuilders\InternalApi\Baskets\Adapters;
            
            use PHPUnit\Framework\MockObject\MockObject;
            use Internal\TestFramework\MockingFramework\Builders\BaseMockBuilder;
            use internal\Baskets\Adapters\HookBasketAdapter;
            
            /**
             * This file was generated using the Symfony command "mock-helpers:generate".
             *
             * Do not edit this file as the content will be replaced automatically the next time someone will run the generate command.
             *
             * If you want to learn more about this framework, visit the documentation on Confluence at:
             * https://example.com/wiki/spaces/COPUNIT/pages/4051238915/Mocking+framework.
             */
            class HookBasketAdapterBuilder extends BaseMockBuilder
            {
                public function __construct(HookBasketAdapter&MockObject $mock)
                {
                    $this->mock = $mock;
                }
            
                public static function getMockedClasses(): array
                {
                    return [HookBasketAdapter::class];
                }
            
                public function getService(): HookBasketAdapter&MockObject
                {
                    return $this->mock;
                }
            }
            
            EOS,
            $this->renderTokensToString($classDef->getTokens(new RenderContext(), $rules)),
        );
    }
}
