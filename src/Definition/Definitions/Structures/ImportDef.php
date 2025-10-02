<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\UseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ImportDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|ClassTypeDef $type,
        public null|string         $alias = null,
    ) {
        if (is_string($this->type)) {
            $this->type = new ClassTypeDef($this->type);
        }
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        $tokens[] = new UseToken();
        $tokens[] = new SpacesToken();
        $tokens[] = $this->type->getSimpleTokens($context);
        if ($this->alias) {
            $tokens[] = new SpacesToken();
            $tokens[] = new AsToken();
            $tokens[] = new SpacesToken();
            $tokens[] = new Token($this->alias);
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
