<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassRefVal extends BaseVal
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public ProvidesClassReference&Tokenizes $name,
    ) {
    }

    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        $tokens[] = $this->name->getSimpleTokens($context);
        $tokens[] = new StaticAccessToken();
        $tokens[] = new ClassToken();
        return $this->flatten($tokens);
    }
}
