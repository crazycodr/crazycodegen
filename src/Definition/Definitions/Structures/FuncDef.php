<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\CanBeCalled;
use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Tokens\LanguageConstructTokenGroups\FunctionDeclarationTokenGroup;
use CrazyCodeGen\Definition\Tokens\LanguageConstructTokenGroups\NamespaceTokenGroup;

class FuncDef implements CanBeComputed, CanBeCalled
{
    use FlattenFunction;

    public function __construct(
        public string      $name,
        /** @var ArgDef[] $arguments */
        public array       $arguments = [],
        public null|string $returnType = null,
        public null|string $namespace = null,
        public array       $bodyInstructions = [],
    )
    {
    }

    public function getTokens(): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = new NamespaceTokenGroup($this->namespace);
        }
        $tokens[] = new FunctionDeclarationTokenGroup(
            name: $this->name,
            arguments: $this->arguments,
            returnType: $this->returnType,
            namespace: $this->namespace,
            bodyInstructions: $this->bodyInstructions,
        );
        return $tokens;
    }

    public function getCallReference(): string
    {
        return $this->name;
    }
}
