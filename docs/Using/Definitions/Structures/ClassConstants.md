# Class constants

Classes can define constants to expose reusable values across their methods or even outside of them for their consumers.

> **Note:** You should expose an Enum if you plan to expose several constants linked together under the same concept. Enum support will be added in a later version.

# Naming your constant

When creating a `ConstantDef`, you must set a `name` that respects PHP identifier practices:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    constants: [
        new ConstantDef(name: 'constA'),
        new ConstantDef(name: 'constB'),    
    ],
);

// Would output
class MyClass
{
    public const constA = null;
    public const constB = null;
}
```

# Changing the visibility

Class constants are by default `public`. You can change the `visibility` by passing one of the different `VisibilityEnum` values:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    constants: [
        new ConstantDef(name: 'constA', visibility: VisibilityEnum::PROTECTED),
        new ConstantDef(name: 'constB', visibility: VisibilityEnum::PRIVATE),    
    ],
);

// Would output
class MyClass
{
    protected const constA = null;
    private const constB = null;
}
```

## Typing

Constants can and should be typed for best-practice reasons. You can pass a `TypeDef` object such as `BuiltInTypeSpec::intType()` or a `ClassTypeDef` object that you generate: (See [TypeDefinitions.md](../TypeDefinitions.md) for more information)

> Note that the example below does not feature `imports` but if you did, using `ClassTypeDef` in constants, it would shorten the identifier in the output code.

> Note that the following example is invalid because the types are not null but the values of the constants are. This is solely for example, you are responsible for configuring the constants properly.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    constants: [
        new ConstantDef(name: 'constA', type: new ClassTypeDef('Foo\Bar\Baz')),
        new ConstantDef(name: 'constB', type: BuiltInTypeSpec::intType()),
    ],
);

// Would output
class MyClass
{
    public const Foo\Bar\Baz constA = null;
    public const int constB = null;
}
```

## Value

A constant without a value (Defaults to `null`) isn't very useful. Note that the value is typed as `mixed` but it actually only accepts valid inferable values from `ValueInferenceTrait::inferValue`. (See [Value definitions](../ValueDefinitions.md)).

```php
$myClass = new ClassDef(
    name: 'MyClass',
    constants: [
        new ConstantDef(name: 'constA', value: 1),
        new ConstantDef(name: 'constB', value: 'Hello world'),
    ],
);

// Would output
class MyClass
{
    public const constA = 1;
    public const constB = 'Hello world';
}
```

# DocBlocks

You can add a `DocBlockDef` to describe the constant.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    constants: [
        new ConstantDef(
            name: 'constA',
            docBlock:  new DocBlockDef(['Used to represent A']),
        ),
    ],
);

// Would output
class MyClass
{
    /**
     * Used to represent A
     */
    public const constA = null;
}
```