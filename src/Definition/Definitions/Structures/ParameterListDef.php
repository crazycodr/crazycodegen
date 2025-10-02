<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ParameterListDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var ParameterDef[] $parameters */
        public array $parameters = [],
    ) {
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        $tokens[] = new ParStartToken();
        $parametersLeft = count($this->parameters);
        foreach ($this->parameters as $parameter) {
            $parametersLeft--;
            $tokens[] = $parameter->getSimpleTokens($context);
            if ($parametersLeft > 0) {
                $tokens[] = new CommaToken();
            }
        }
        $tokens[] = new ParEndToken();
        return $this->flatten($tokens);
    }
}
