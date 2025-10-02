# Type Definitions

*Type definitions* represent the building blocks for declaring types in method signatures, properties, parameters, and return types.

These types can be simple scalar types, complex union/intersection types, or contextual references like `self` and `static`.

## Overview

Each type definition is represented by a class implementing the `TypeDef` interface, and can be rendered into tokens for final code generation. Here's a breakdown of all supported types:

## 1. Built-in Types

The `BuiltInTypeSpec` class wraps native PHP types. You should use this when referencing scalar, compound, or special types built into the PHP language.

### Supported Types

- `int`
- `float`
- `bool`
- `string`
- `array`
- `object`
- `callable`
- `void`
- `true`
- `false`
- `null`
- `mixed`
- `iterable`

### Example

```php
$intType = BuiltInTypeSpec::intType();
$stringType = new BuiltInTypeSpec(BuiltInTypesEnum::string);
```

## 2. Class Types

The `ClassTypeDef` is used for any class or interface name, including:

- Built-in PHP classes (e.g. `SplFileInfo`)
- Third-party libraries (e.g. `PHPUnit\Framework\TestCase`)
- Your application classes (e.g. `App\Services\Logger`)

### Example

```php
$redisClient = new ClassTypeDef('Redis');
$customWrapper = new ClassDef(name: 'MyClass', extends: $redisClient);
```

It automatically resolves fully-qualified names and determines whether short names can be used depending on import context.

## 3. Contextual Types

These types represent special context-sensitive references:

- `SelfTypeSpec` → represents the `self` keyword
- `StaticTypeSpec` → represents the `static` keyword

Both classes implement `ShouldBeAccessedStatically` and `ProvidesClassReference` which are used by internal components to properly render them as statically accessed component.

For example:

```php
$ref = new SelfTypeSpec();
$ref->to(new CallOp($methodToBeDefined))->getTokens();

// Once serialized, this will yield:
self::methodToBeDefined()
```

## 4. Multi-Type Definitions

The `MultiTypeDef` class enables union and intersection types:

### Union Example (`null|int`):

```php
new MultiTypeDef([
    BuiltInTypeSpec::nullType(),
    BuiltInTypeSpec::intType(),
]);
```

### Intersection Example (`A&B`):

```php
new MultiTypeDef([
    new ClassTypeDef('A'),
    new ClassTypeDef('B'),
], unionTypes: false);
```

### Nested Groups Example (`(int|string)&(bool|float)`)

Be careful when using nested groups, this feature is available in PHP 8.2+. This library does not prevent you from generating code that is not valid for your target version.

```php
new MultiTypeDef([
    new MultiTypeDef(['int', 'string'], nestedTypes: true),
    new MultiTypeDef(['bool', 'float'], nestedTypes: true),
], unionTypes: false);
```

## 5. Type Inference

The `TypeInferenceTrait` allows resolving a type from string input:

```php
$type = $this->inferType('int');     // → BuiltInTypeSpec
$type = $this->inferType('self');    // → SelfTypeSpec
$type = $this->inferType('MyClass'); // → ClassTypeDef
```

You can use this approach in your own code to easily generate `TypeDef` objects. Note that `TypeInferenceTrait` does not support parsing union or intersection types, only simple classes or built in types.