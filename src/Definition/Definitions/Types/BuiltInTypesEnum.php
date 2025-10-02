<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

enum BuiltInTypesEnum: string
{
    case int = 'int';
    case float = 'float';
    case bool = 'bool';
    case string = 'string';
    case array = 'array';
    case object = 'object';
    case callable = 'callable';
    case void = 'void';
    case true = 'true';
    case false = 'false';
    case null = 'null';
    case mixed = 'mixed';
    case iterable = 'iterable';
}
