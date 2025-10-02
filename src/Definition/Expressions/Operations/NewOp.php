<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesClassType;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Structures\ClassDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NewToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class NewOp extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;
    use ValueInferenceTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public string|Tokenizes|ClassTypeDef|ClassDef $class,
        /** @var mixed[]|array<Tokenizes> $arguments */
        public array                                  $arguments = [],
    ) {
        if (is_string($this->class)) {
            $this->class = new ClassTypeDef($this->class);
        } elseif ($this->class instanceof ProvidesClassType) {
            $this->class = $this->class->getClassType();
        }
        foreach ($this->arguments as $argumentIndex => $argument) {
            if ($this->isInferableValue($argument)) {
                $this->arguments[$argumentIndex] = $this->inferValue($argument);
            } elseif (!$argument instanceof Tokenizes) {
                unset($this->arguments[$argumentIndex]);
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new NewToken();
        $tokens[] = new SpacesToken();
        if ($this->class instanceof Tokenizes) {
            $tokens[] = $this->class->getTokens($context);
        } else {
            $tokens[] = $this->class;
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
