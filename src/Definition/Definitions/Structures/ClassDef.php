<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Renderers\ContextShift;
use CrazyCodeGen\Definition\Renderers\ContextShiftOperationEnum;
use CrazyCodeGen\Definition\Renderers\ContextTypeEnum;
use CrazyCodeGen\Definition\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Definition\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Definition\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Definition\Tokens\KeywordTokens\AbstractToken;
use CrazyCodeGen\Definition\Tokens\KeywordTokens\ClassToken;
use CrazyCodeGen\Definition\Tokens\KeywordTokens\ExtendsToken;
use CrazyCodeGen\Definition\Tokens\KeywordTokens\ImplementsToken;
use CrazyCodeGen\Definition\Tokens\KeywordTokens\NamespaceToken;
use CrazyCodeGen\Definition\Tokens\KeywordTokens\VisibilityToken;
use CrazyCodeGen\Definition\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;
use CrazyCodeGen\Definition\Tokens\UserLandTokens\IdentifierToken;
use CrazyCodeGen\Definition\Tokens\UserLandTokens\NamespacePathToken;

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
