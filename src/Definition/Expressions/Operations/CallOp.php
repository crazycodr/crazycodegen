<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class CallOp extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;
    use ValueInferenceTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public string|Expression|ProvidesCallableReference|Tokenizes $subject,
        /** @var mixed[]|ProvidesClassReference[] $arguments */
        public array                                       $arguments = [],
    ) {
        if ($this->subject instanceof ProvidesCallableReference) {
            $this->subject = $this->subject->getCallableReference();
        } elseif (is_string($this->subject)) {
            $this->subject = new Expression($this->subject);
        }
        foreach ($this->arguments as $argumentIndex => $argument) {
            if ($this->isInferableValue($argument)) {
                $this->arguments[$argumentIndex] = $this->inferValue($argument);
            } elseif ($argument instanceof ProvidesClassReference) {
                $this->arguments[$argumentIndex] = $argument->getClassReference();
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->subject instanceof Tokenizes) {
            $tokens[] = $this->subject->getTokens($context);
        } else {
            $tokens[] = $this->subject;
        }
        $tokens[] = new ParStartToken();
        $argumentsLeft = count($this->arguments);
        $argumentTokens = [];
        foreach ($this->arguments as $argument) {
            $argumentsLeft--;
            if ($argument instanceof Token) {
                $argumentTokens[] = $argument;
            } else {
                $argumentTokens[] = $argument->getTokens($context);
            }
            if ($argumentsLeft > 0) {
                $argumentTokens[] = new CommaToken();
            }
        }
        $tokens[] = $argumentTokens;
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
