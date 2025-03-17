<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Structures\MethodDefScenarios;

use CrazyCodeGen\Definition\Definitions\Contexts\ParentContext;
use CrazyCodeGen\Definition\Definitions\Contexts\ThisContext;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Expressions\Operations\CallOp;
use CrazyCodeGen\Definition\Expressions\Operations\NewOp;
use CrazyCodeGen\Definition\Expressions\Operations\ReturnOp;
use CrazyCodeGen\Definition\Expressions\Operators\Assignment\AssignOp;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ContextMemberAccessScenarioTest extends TestCase
{
    use TokenFunctions;

    public function testUsingThisContextToChainAccessesToArgumentsReturnsTheProperCode()
    {
        $modelTypePropertyType = new ClassTypeDef('Internal\Project\Models\Model');

        $modelTypeProperty = new PropertyDef(
            'modelType',
            type: 'string',
            defaultValue: $modelTypePropertyType
        );

        $modelProperty = new PropertyDef(
            'model',
            type: $modelTypePropertyType->asNullable(),
            defaultValue: null
        );

        $getMethod = (new MethodDef('get'))
            ->setReturnType($modelProperty->type)
            ->addInstruction(new AssignOp(
                ThisContext::to($modelProperty),
                new NewOp($modelTypePropertyType),
            ))
            ->addInstruction(new ReturnOp(ThisContext::to($modelProperty)));

        $classDef = (new ClassDef('ContextMemberAccessScenario'))
            ->setNamespace('Internal\Project\Models')
            ->addImport($modelTypePropertyType)
            ->addProperty($modelTypeProperty)
            ->addProperty($modelProperty)
            ->addMethod($getMethod);

        $this->assertEquals(
            <<<'EOS'
            namespace Internal\Project\Models;
            
            use Internal\Project\Models\Model;
            
            class ContextMemberAccessScenario
            {
                public string $modelType = Model::class;
                public Model|null $model = null;
            
                public function get(): Model|null
                {
                    $this->model = new Model();
                    return $this->model;
                }
            }
            
            EOS,
            $this->renderTokensToString($classDef->getTokens(new RenderContext(), new RenderingRules())),
        );
    }

    public function testUsingParentContextToChainAccessesToParentMemberReturnsTheProperCode()
    {
        $constructor = (new MethodDef('__construct'))
            ->addInstruction(ParentContext::to(new CallOp('__construct')));

        $classDef = (new ClassDef('ContextMemberAccessScenario'))
            ->setNamespace('Internal\Project\Models')
            ->addMethod($constructor);

        $this->assertEquals(
            <<<'EOS'
            namespace Internal\Project\Models;
            
            class ContextMemberAccessScenario
            {
                public function __construct()
                {
                    parent::__construct();
                }
            }
            
            EOS,
            $this->renderTokensToString($classDef->getTokens(new RenderContext(), new RenderingRules())),
        );
    }
}
