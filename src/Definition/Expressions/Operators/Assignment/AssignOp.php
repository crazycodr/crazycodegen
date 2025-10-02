<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assignment;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;

class AssignOp extends Tokenizes
{
    use FlattenFunction;
    use ValueInferenceTrait;

    public readonly Tokenizes $subject;
    public readonly Tokenizes $value;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        string|Tokenizes|ProvidesVariableReference $subject,
        mixed $value,
    ) {
        if (is_string($subject)) {
            $this->subject = new Expression($subject);
        } elseif ($subject instanceof ProvidesVariableReference) {
            $this->subject = $subject->getVariableReference();
        } else {
            $this->subject = $subject;
        }
        if ($value instanceof ProvidesClassReference) {
            $this->value = $value->getClassReference();
        } elseif ($value instanceof ProvidesVariableReference) {
            $this->value = $value->getVariableReference();
        } elseif ($value instanceof Tokenizes) {
            $this->value = $value;
        } else {
            $this->value = $this->inferValue($value);
        }
    }

    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] =  $this->subject->getTokens($context);
        $tokens[] =  new EqualToken();
        $tokens[] =  $this->value->getTokens($context);
        return $this->flatten($tokens);
    }
}
