<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesChopDownTokens;
use CrazyCodeGen\Definition\Base\ProvidesInlineTokens;
use CrazyCodeGen\Definition\Base\ProvidesClassType;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Structures\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Traits\ComputableTrait;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NewToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class NewOp extends Tokenizes implements ProvidesInlineTokens, ProvidesChopDownTokens
{
    use FlattenFunction;
    use TokenFunctions;
    use ComputableTrait;

    public function __construct(
        public string|Token|Tokenizes|ClassTypeDef|ClassDef $class,
        /** @var array|Tokenizes[] $arguments */
        public array                                        $arguments = [],
    ) {
        if (is_string($this->class)) {
            $this->class = new ClassTypeDef($this->class);
        } elseif ($this->class instanceof ClassTypeDef) {
            // Do nothing, already a type
        } elseif ($this->class instanceof ProvidesClassType) {
            $this->class = $this->class->getClassType();
        }
        foreach ($this->arguments as $argumentIndex => $argument) {
            if ($this->isScalarType($argument)) {
                $this->arguments[$argumentIndex] = $this->getValOrReturn($argument);
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getChopDownTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new NewToken();
        $tokens[] = new SpacesToken();
        if ($this->class instanceof Tokenizes) {
            $tokens[] = $this->class->getTokens($context, $rules);
        } else {
            $tokens[] = $this->class;
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
        return $this->getInlineTokens($context, $rules);
    }

    /**
     * @return Token[]
     */
    public function getInlineTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new NewToken();
        $tokens[] = new SpacesToken();
        if ($this->class instanceof Tokenizes) {
            $tokens[] = $this->class->getTokens($context, $rules);
        } else {
            $tokens[] = $this->class;
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
