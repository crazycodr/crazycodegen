<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDef;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassRef extends Expression
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|SingleTypeDef $name,
    ) {
        if (is_string($this->name)) {
            $this->name = new SingleTypeDef($this->name);
        }
        parent::__construct([$this->name, new StaticAccessToken(), new ClassToken()]);
    }
}
