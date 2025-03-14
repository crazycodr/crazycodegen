<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assignment;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class AssignOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    public function __construct(
        public string|Tokenizes|ProvidesVariableReference                                       $subject,
        public mixed $value,
    ) {
        if (is_string($this->subject)) {
            $this->subject = new Expression($this->subject);
        } elseif ($this->subject instanceof ProvidesVariableReference) {
            $this->subject = $this->subject->getVariableReference();
        }
        if ($this->isInferableValue($this->value)) {
            $this->value = $this->inferValue($this->value);
        } elseif ($this->value instanceof ProvidesClassReference) {
            $this->value = $this->value->getClassReference();
        } elseif ($this->value instanceof ProvidesVariableReference) {
            $this->value = $this->value->getVariableReference();
        } elseif (!$this->value instanceof Tokenizes) {
            $this->value = $this->inferValue(null);
        }
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] =  $this->subject->getTokens($context, $rules);
        $tokens[] =  new SpacesToken();
        $tokens[] =  new EqualToken();
        $tokens[] =  new SpacesToken();
        $tokens[] =  $this->value->getTokens($context, $rules);
        return $this->flatten($tokens);
    }
}
