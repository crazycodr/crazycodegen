<?php

namespace CrazyCodeGen\Definition\Definitions\Structures\Types;

trait TypeInferenceTrait
{
    public function inferAnyType(string $type): TypeDef
    {
        return match ($type) {
            'self' => new SelfTypeSpec(),
            'static' => new StaticTypeSpec(),
            'null' => new NullTypeSpec(),
            'void' => new VoidTypeSpec(),
            'mixed' => new MixedTypeSpec(),
            'int', 'float', 'bool', 'string', 'array', 'callable', 'true', 'false' => new BuiltInTypeSpec($type),
            default => new ClassTypeDef($type),
        };
    }

    public function inferVariableOnlyType(string $type): TypeDef
    {
        return match ($type) {
            'self' => new SelfTypeSpec(),
            'static' => new StaticTypeSpec(),
            'mixed' => new MixedTypeSpec(),
            'int', 'float', 'bool', 'string', 'array', 'callable', 'true', 'false' => new BuiltInTypeSpec($type),
            default => new BuiltInTypeSpec('string'),
        };
    }
}
