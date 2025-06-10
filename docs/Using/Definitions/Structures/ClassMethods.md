# Class methods

Classes typically define methods to operate on their properties or handle passed-in arguments. To define class methods, you must use the `MethodDef` class and add them to the `methods` array of the `ClassDef` object you are defining.

# Naming your method

When creating a `MethodDef`, you must set a `name` that respects PHP identifier practices:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(name: 'methodA'),
        new MethodDef(name: 'methodB'),    
    ],
);

// Would output
class MyClass
{
    public function methodA()
    {
    }
    
    public function methodB()
    {
    }
}
```

# Changing the visibility

Class methods are by default `public`. You can change the `visibility` by passing one of the different `VisibilityEnum` values:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(name: 'methodA', visibility: VisibilityEnum::PROTECTED),
        new MethodDef(name: 'methodB', visibility: VisibilityEnum::PRIVATE),    
    ],
);

// Would output
class MyClass
{
    protected function methodA()
    {
    }
    
    private function methodB()
    {
    }
}
```

# Modifiers

Modifiers supported by the framework for class methods are `abstract` and `static`. These are simple boolean values that are `false` by default:

> **Note**: You must manually set the `abstract` modifier on the `ClassDef` if one of the methods is set `abstract` or it will generate an invalid class definition like the one below.

## Abstract method requirements

> If any method is marked `abstract`, you must also set `abstract: true` on the `ClassDef`, or the generated class will be invalid.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(name: 'methodA', static: true),
        new MethodDef(name: 'methodB', abstract: true),
    ],
);

// Would output
class MyClass
{
    public static function methodA()
    {
    }
    
    abstract public function methodB();
}
```

> Support for `final` modifier will be added in a later release.

# Method parameters

Class parameters are defined by passing either a `string` or a `ParameterDef` object into the `parameters` parameter of the `MethodDef` class. When a parameter is defined as a `string`, it is automatically converted into a `ParameterDef` with that name and no type or default value.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                'paramA',
                new ParameterDef(name: 'paramB'),            
            ],
        ),
    ],
);

// Would output
class MyClass
{
    public function methodA($paramA, $paramB)
    {
    }
}
```

## Parameter typing

Parameters can and should be typed for best-practice reasons. You can pass a `string` to the `type` property and the type will be inferred automatically into a valid `TypeDef` object:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                new ParameterDef(name: 'paramA', type: 'int'),
                new ParameterDef(name: 'paramB', type: 'array'),            
            ],
        ),
    ],
);

// Would output
class MyClass
{
    public function methodA(int $paramA, array $paramB)
    {
    }
}
```

> See [Type definitions](../TypeDefinitions.md) for more information.

## Variadic parameters

If you need a parameter to be variadic (See [Variable argument lists](https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list)), you can set the `variadic` flag to true. This will generate the following code:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                new ParameterDef(name: 'paramA', type: 'int'),
                new ParameterDef(name: 'paramB', type: 'int', variadic: true),            
            ],
        ),
    ],
);

// Would output
class MyClass
{
    public function methodA(int $paramA, int ...$paramB)
    {
    }
}
```

## Parameter default value

Sometimes, a parameter will feature a default value. To set one, just pass anything to `defaultValue` and the default value will be transformed into a valid expression as much as possible.

The `defaultValue` is not `null` by default, it is `@!#UNSET@!#` so that the framework can detect that you want to set a `null` default. Therefore, the only impossible default value to set on a parameter using this framework is `@!#UNSET@!#`.

Also note that the value is typed as `mixed` but it actually only accepts valid inferable values from `ValueInferenceTrait::inferValue`. (See [Value definitions](../ValueDefinitions.md)).

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                new ParameterDef(name: 'paramA', defaultValue: null),
                new ParameterDef(name: 'paramB', defaultValue: ['foo' => 'bar']),            
            ],
        ),
    ],
);

// Would output
class MyClass
{
    public function methodA($paramA = null, $paramB = ['foo' => 'bar'])
    {
    }
}
```

# Return type

A method's return type can and should be always specified for best-practice reasons. You can pass a `string` to the `returnType` property and the type will be inferred automatically into a valid `TypeDef` object:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(name: 'methodA', returnType: 'int'),
    ],
);

// Would output
class MyClass
{
    public function methodA(): int
    {
    }
}
```

> See [Type definitions](../TypeDefinitions.md) for more information.

# DocBlocks

You can add a DocBlock to describe the method. This is especially important when using parameters or return types that are of the `array` type so that IDEs can guide the developer with what is acceptable.

To add a docblock, you can pass a single `string`, a `string[]` or a `DocBlockDef` object. You can also pass a `string` or an array of strings — these will be automatically wrapped in a `DocBlockDef`. This produces the same result as explicitly passing a `DocBlockDef`:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            docBlock: [
                'Contains the "A" data',
                '@param string[] $paramA',
            ],
            parameters: ['paramA']
        ),    
    ],
);

// Would output
class MyClass
{
    /**
     * Contains the "A" data. 
     * @param string[] $paramA 
     */
    public function methodA($paramA)
    {
    }
}
```

# Instructions

Instructions are themselves a very feature-rich topic. The whole subject is addressed under the [Instructions](../../Instructions.md) section.

Instructions are passed as an array of different objects into the `instructions` parameter. See the documentation to understand how `instructions` are processed and converted.