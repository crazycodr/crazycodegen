<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Rendering\Renderers\Contexts\ContextShift;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextShiftOperationEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ExtendsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ImplementsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\VisibilityToken;
use CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Rendering\Tokens\UserLandTokens\IdentifierToken;

class ClassDef implements CanBeComputed, CanBeCalled
{
    use FlattenFunction;

    public function __construct(
        public string      $name,
        public null|string $extends = null,
        public null|VisibilityEnum $visibility = null,
        public bool $abstract = false,
        public array $implements = [],
        public null|string $namespace = null,
    )
    {
    }

    public function getTokens(): array
    {
        $tokens = [];
        $tokens[] = new ContextShift(ContextShiftOperationEnum::push, ContextTypeEnum::classDeclaration);
        if ($this->namespace) {
            $tokens[] = new NamespaceTokenGroup($this->namespace);
        }
        if ($this->visibility) {
            $tokens[] = new VisibilityToken($this->visibility);
        }
        if ($this->abstract) {
            $tokens[] = new AbstractToken();
        }
        $tokens[] = new ClassToken();
        $tokens[] = new IdentifierToken($this->name);
        if ($this->extends) {
            $tokens[] = new ExtendsToken($this->extends);
        }
        if (!empty($this->implements)) {
            $tokens[] = new ImplementsToken($this->implements);
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new BraceEndToken();
        $tokens[] = new ContextShift(ContextShiftOperationEnum::pop, ContextTypeEnum::classDeclaration);
        return $tokens;
    }

    public function getCallReference(): string
    {
        return $this->name;
    }
}
