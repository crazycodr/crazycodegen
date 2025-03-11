<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Definitions\Structures\SingleTypeDefinition;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ClassReferenceTokenGroup extends ExpressionTokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public string|SingleTypeDefinition $name,
    ) {
        if (is_string($this->name)) {
            $this->name = new SingleTypeDefinition($this->name);
        }
        parent::__construct([$this->name, new StaticAccessToken(), new ClassToken()]);
    }
}
