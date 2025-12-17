<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\UseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

/**
 * Simple trait usage statement, does not support conflict resolution yet nor the ability
 * to import multiple traits in the same use statement.
 */
class UseTraitDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public readonly ClassTypeDef $trait;

    public function __construct(
        string|ClassTypeDef $type,
    ) {
        if (is_string($type)) {
            $this->trait = new ClassTypeDef($type);
        } else {
            $this->trait = $type;
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
        $tokens[] = $this->trait->getTokens($context);
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
