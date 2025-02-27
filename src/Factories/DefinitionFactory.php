<?php

namespace CrazyCodeGen\Factories;

use CrazyCodeGen\Definitions\Structures\FuncDef;
use CrazyCodeGen\Definitions\Values\BoolValue;
use CrazyCodeGen\Definitions\Values\FloatValue;
use CrazyCodeGen\Definitions\Values\IntValue;
use CrazyCodeGen\Definitions\Values\StringValue;
use CrazyCodeGen\Definitions\Values\Variable;

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

    public function stringVal(string $value): StringValue
    {
        return new StringValue($value);
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
        return new FuncDef(name: $name, arguments: $arguments, returnType: $returnType, namespace: $namespace, body: $body);
    }
}