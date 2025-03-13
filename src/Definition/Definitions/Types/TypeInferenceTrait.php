<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

trait TypeInferenceTrait
{
    public function inferType(string $type): TypeDef
    {
        if (BuiltInTypeSpec::supports($type)) {
            return new BuiltInTypeSpec($type);
        }
        return match ($type) {
            'self' => new SelfTypeSpec(),
            'static' => new StaticTypeSpec(),
            default => new ClassTypeDef($type),
        };
    }
}
