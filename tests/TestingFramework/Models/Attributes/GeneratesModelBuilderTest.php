<?php

namespace CrazyCodeGen\Tests\TestingFramework\Models\Attributes;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use CrazyCodeGen\TestingFramework\Models\Attributes\GeneratesModelBuilder;
use PHPUnit\Framework\TestCase;

class GeneratesModelBuilderTest extends TestCase
{
    use TokenFunctions;

    public function testAbilityToGenerateModelHelperFromPreviousInternalFramework()
    {
        $modelBuilder = new GeneratesModelBuilder(target: 'Internal\Service\Models\Model');

        $rules = new RenderingRules();
        $rules->docBlocks->lineLength = 125;

        $classes = $modelBuilder->getClasses();
        $this->assertCount(1, $classes);

        $this->assertEquals(
            <<<'EOS'
            use Internal\Service\Models\Model;
            
            class ModelModelBuilder
            {
                protected Model $model;
            
                protected function createModel(): Model
                {
                    return new Model();
                }
            
                public function restart(): self
                {
                    $this->model = $this->createModel();
                    return $this;
                }
            
                public function get(): Model
                {
                    return $this->model;
                }
            }
            
            EOS,
            $this->renderTokensToString($classes[0]->getTokens(new RenderContext(), $rules)),
        );
    }
}
