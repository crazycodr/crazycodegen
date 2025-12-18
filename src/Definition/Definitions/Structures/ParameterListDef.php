<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ParameterListDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var array<PropertyDef|ParameterDef> $parameters */
        public array $parameters = [],
    ) {
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new ParStartToken();
        $parametersLeft = count($this->parameters);
        foreach ($this->parameters as $parameter) {
            $parametersLeft--;
            $parameterTokens = $parameter->getTokens($context);
            if ($parameterTokens[array_key_last($parameterTokens)] instanceof SemiColonToken) {
                array_pop($parameterTokens);
            }
            $tokens[] = $parameterTokens;
            if ($parametersLeft > 0) {
                $tokens[] = new CommaToken();
            }
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
