<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDef;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassRefVal extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|SingleTypeDef $name,
    ) {
        if (is_string($this->name)) {
            $this->name = new SingleTypeDef($this->name);
        }
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $token = [];
        $tokens[] = $this->name->getTokens($context, $rules);
        $tokens[] = new StaticAccessToken();
        $tokens[] = new ClassToken();
        return $this->flatten($tokens);
    }
}
