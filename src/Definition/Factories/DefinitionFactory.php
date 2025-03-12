<?php

namespace CrazyCodeGen\Definition\Factories;

use CrazyCodeGen\Definition\Definitions\Structures\FuncDef;
use CrazyCodeGen\Definition\Definitions\Values\BoolValue;
use CrazyCodeGen\Definition\Definitions\Values\FloatValue;
use CrazyCodeGen\Definition\Definitions\Values\IntValue;
use CrazyCodeGen\Definition\Definitions\Values\OldStringValue;
use CrazyCodeGen\Definition\Definitions\Values\Variable;

class DefinitionFactory
{
    public function intVal(int $value): IntValue
    {
        return new IntValue($value);
    }

    public function floatVal(float $value): FloatValue
    {
        return new FloatValue($value);
    }

    public function stringVal(string $value): OldStringValue
    {
        return new OldStringValue($value);
    }

    public function boolVal(bool $value): BoolValue
    {
        return new BoolValue($value);
    }

    public function var(string $name): Variable
    {
        return new Variable($name);
    }

    public function funcDef(string $name, array $arguments = [], null|string $returnType = null, null|string $namespace = null, array $body = []): FuncDef
    {
        return new FuncDef(name: $name, arguments: $arguments, returnType: $returnType, namespace: $namespace, bodyInstructions: $body);
    }
}
