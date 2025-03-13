<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesCallableReference;
use CrazyCodeGen\Definition\Base\ProvidesChopDownTokens;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\ProvidesInlineTokens;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class CallOp extends Tokenizes implements ProvidesInlineTokens, ProvidesChopDownTokens
{
    use FlattenFunction;
    use TokenFunctions;
    use ValueInferenceTrait;

    public function __construct(
        public string|Expression|ProvidesCallableReference $subject,
        public array                            $arguments = [],
    )
    {
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
    public function getChopDownTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->subject instanceof Tokenizes) {
            $tokens[] = $this->subject->getTokens($context, $rules);
        } else {
            $tokens[] = $this->subject;
        }
        $tokens[] = new ParStartToken();
        if (!empty($this->arguments)) {
            $tokens[] = new NewLinesToken();
        }
        $argumentsLeft = count($this->arguments);
        $argumentTokens = [];
        foreach ($this->arguments as $argument) {
            $argumentsLeft--;
            if ($argument instanceof ProvidesChopDownTokens) {
                $argumentTokens[] = $argument->getChopDownTokens($context, $rules);
            } elseif ($argument instanceof Token) {
                $argumentTokens[] = $argument;
            } else {
                $argumentTokens[] = $argument->getTokens($context, $rules);
            }
            $argumentTokens[] = new CommaToken();
            if ($argumentsLeft > 0) {
                $argumentTokens[] = new NewLinesToken();
            }
        }
        if (!empty($this->arguments)) {
            $tokens[] = $this->insertIndentationTokens($rules, $argumentTokens);
            $tokens[] = new NewLinesToken();
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $inlineScenario = $this->getInlineTokens($context, $rules);
        if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
            $tokens[] = $inlineScenario;
        } else {
            $tokens[] = $this->getChopDownTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function getInlineTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->subject instanceof Tokenizes) {
            $tokens[] = $this->subject->getTokens($context, $rules);
        } else {
            $tokens[] = $this->subject;
        }
        $tokens[] = new ParStartToken();
        $argumentsLeft = count($this->arguments);
        $argumentTokens = [];
        foreach ($this->arguments as $argument) {
            $argumentsLeft--;
            if ($argument instanceof ProvidesInlineTokens) {
                $argumentTokens[] = $argument->getInlineTokens($context, $rules);
            } elseif ($argument instanceof Token) {
                $argumentTokens[] = $argument;
            } else {
                $argumentTokens[] = $argument->getTokens($context, $rules);
            }
            if ($argumentsLeft > 0) {
                $argumentTokens[] = new CommaToken();
                $argumentTokens[] = new SpacesToken();
            }
        }
        $tokens[] = $argumentTokens;
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
