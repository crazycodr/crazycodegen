<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Defines;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\UseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ImportDef extends Defines
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|SingleTypeDef $type,
        public null|string          $alias = null,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new UseToken();
        $tokens[] = new SpacesToken($rules->imports->spacesAfterUse);
        if (is_string($this->type)) {
            $tokens[] = (new SingleTypeDef($this->type))->getTokens($context, $rules);
        } else {
            $tokens[] = $this->type->getTokens($context, $rules);
        }
        if ($this->alias) {
            $tokens[] = new SpacesToken($rules->imports->spacesAfterType);
            $tokens[] = new AsToken();
            $tokens[] = new SpacesToken($rules->imports->spacesAfterAs);
            $tokens[] = new Token($this->alias);
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
