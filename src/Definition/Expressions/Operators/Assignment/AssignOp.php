<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assignment;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;

class AssignOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
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

    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        $tokens[] =  $this->subject->getSimpleTokens($context);
        $tokens[] =  new EqualToken();
        $tokens[] =  $this->value->getSimpleTokens($context);
        return $this->flatten($tokens);
    }
}
