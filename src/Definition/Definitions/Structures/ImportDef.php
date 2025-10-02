<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
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

    public readonly ClassTypeDef $type;

    public function __construct(
        string|ClassTypeDef $type,
        public null|string         $alias = null,
    ) {
        if (is_string($type)) {
            $this->type = new ClassTypeDef($type);
        } else {
            $this->type = $type;
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new UseToken();
        $tokens[] = new SpacesToken();
        $tokens[] = $this->type->getTokens($context);
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
