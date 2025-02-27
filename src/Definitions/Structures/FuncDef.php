<?php

namespace CrazyCodeGen\Definitions\Structures;

use CrazyCodeGen\Base\CanBeCalled;
use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Expressions\Structures\Instruction;
use CrazyCodeGen\Renderers\ContextShift;
use CrazyCodeGen\Renderers\ContextTypeEnum;
use CrazyCodeGen\Traits\FlattenFunction;

class FuncDef implements CanBeComputed, CanBeCalled
{
    use FlattenFunction;

    public function __construct(
        public string      $name,
        /** @var ArgDef[] $arguments */
        public array       $arguments = [],
        public null|string $returnType = null,
        public null|string $namespace = null,
        public array $body = [],
    )
    {
    }

    public function getTokens(): array
    {
        $tokens = [];
        if ($this->namespace) {
            $tokens[] = ContextShift::push(ContextTypeEnum::namespace);
            $tokens[] = 'namespace';
            $tokens[] = ContextShift::push(ContextTypeEnum::namespaceName);
            $tokens[] = $this->namespace;
            $tokens[] = ContextShift::pop(ContextTypeEnum::namespaceName);
            $tokens[] = ';';
            $tokens[] = ContextShift::pop(ContextTypeEnum::namespace);
        }
        $tokens[] = ContextShift::push(ContextTypeEnum::function);
        $tokens[] = 'function';
        $tokens[] = $this->name;
        $tokens[] = ContextShift::push(ContextTypeEnum::argumentList);
        $tokens[] = '(';
        $tokens = $this->addArguments($tokens);
        $tokens[] = ')';
        $tokens[] = ContextShift::pop(ContextTypeEnum::argumentList);
        if ($this->returnType) {
            $tokens[] = ContextShift::push(ContextTypeEnum::returnType);
            $tokens[] = ':';
            $tokens[] = ContextShift::push(ContextTypeEnum::type);
            $tokens[] = $this->returnType;
            $tokens[] = ContextShift::pop(ContextTypeEnum::type);
            $tokens[] = ContextShift::pop(ContextTypeEnum::returnType);
        }
        $tokens[] = ContextShift::push(ContextTypeEnum::block);
        $tokens[] = '{';
        $tokens[] = ContextShift::push(ContextTypeEnum::blockBody);
        $tokens = $this->addBody($tokens);
        $tokens[] = ContextShift::pop(ContextTypeEnum::blockBody);
        $tokens[] = '}';
        $tokens[] = ContextShift::pop(ContextTypeEnum::block);
        $tokens[] = ContextShift::pop(ContextTypeEnum::function);
        return $tokens;
    }

    public function getCallReference(): string
    {
        return $this->name;
    }

    private function addArguments(array $tokens): array
    {
        foreach ($this->arguments as $argument) {
            $tokens = $this->flatten(array_merge($tokens, $argument->getTokens()));
        }
        return $tokens;
    }

    private function addBody(array $tokens): array
    {
        foreach ($this->body as $expression) {
            $instruction = new Instruction($expression);
            $tokens = $this->flatten(array_merge($tokens, $instruction->getTokens()));
        }
        return $tokens;
    }
}
