<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

trait TypeInferenceTrait
{
    public function inferType(string $type): TypeDef
    {
        if ($builtInType = BuiltInTypesEnum::tryFrom($type)) {
            return new BuiltInTypeSpec($builtInType);
        }
        return match ($type) {
            'self' => new SelfTypeSpec(),
            'static' => new StaticTypeSpec(),
            default => new ClassTypeDef($type),
        };
    }
}
