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

Modifiers supported by the library for class methods are `abstract` and `static`. These are simple boolean values that are `false` by default:

## Abstract methods

By setting a method `abstract: true`, it will add the keyword to the method.

> **Warning:** If any method is marked `abstract`, you must also set `abstract: true` on the `ClassDef`, or the generated class will be invalid. See example below. You cannot have a non-abstract class with an abstract method, the library will not fix this for you.

> Support for `final` modifier will be added in a later release.

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

# Method parameters

Class parameters are defined by passing a `ParameterDef` object into the `parameters` parameter of the `MethodDef` class.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                new ParameterDef(name: 'paramA'),
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

Parameters can and should be typed for best-practice reasons. You can pass a `TypeDef` object such as `BuiltInTypeSpec::intType()` or a `ClassTypeDef` object that you generate: (See [TypeDefinitions.md](../TypeDefinitions.md) for more information)

> Note that the example below does not feature `imports` but if you did, using `ClassTypeDef` in parameters, it would shorten the identifier in the output code.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                new ParameterDef(name: 'paramA', type: new ClassTypeDef('Foo\Bar\Baz')),
                new ParameterDef(name: 'paramB', type: BuiltInTypeSpec::intType()),
            ],
        ),
    ],
);

// Would output
class MyClass
{
    public function methodA(Foo\Bar\Baz $paramA, int $paramB)
    {
    }
}
```

## Variadic parameters

If you need a parameter to be variadic (See [Variable argument lists](https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list)), you can set the `variadic` flag to true. This will generate the following code:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            parameters: [
                new ParameterDef(name: 'paramA', type: BuiltInTypeSpec::intType()),
                new ParameterDef(name: 'paramB', type: BuiltInTypeSpec::intType(), variadic: true),            
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

The `defaultValue` is not `null` by default, it is `@!#UNSET@!#` so that the library can detect that you want to set a `null` default. Therefore, the only impossible default value to set on a parameter using this library is `@!#UNSET@!#`.

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

A method's return type can and should be typed for best-practice reasons. You can pass a `TypeDef` object such as `BuiltInTypeSpec::intType()` or a `ClassTypeDef` object that you generate: (See [TypeDefinitions.md](../TypeDefinitions.md) for more information)

> Note that the example below does not feature `imports` but if you did, using `ClassTypeDef` in the return type, it would shorten the identifier in the output code.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(name: 'methodA', returnType: BuiltInTypeSpec::intType()),
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

# DocBlocks

You can add a `DocBlockDef` to describe the property. This is especially important when using parameters or return types that are of the `array` type so that IDEs can guide the developer with what is acceptable.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    methods: [
        new MethodDef(
            name: 'methodA',
            docBlock:  new DocBlockDef([
                'Contains the "A" data',
                '@param string[] $paramA',
            ]),
            parameters: [
                new ParameterDef('paramA')
            ],
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

# Constructor promoted properties

Constructor promoted properties are an 8.0 addition that allows developers to save on assignment calls in class constructors.

Class constructors must be declared manually in this framework. When you create a `MethodDef` that is named `__construct`, you can pass in `PropertyDef` objects along with `ParameterDef` objects to the `parameters` argument and these will be rendered as promoted properties. Passing `PropertyDef` objects to `parameters` in a `MethodDef` that is not named `__construct` will result in an error. You must not declare them in the `ClassDef` and in the `MethodDef` or you'll end up with runtime errors.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    parameters: [
        new PropertyDef(name: 'myPropA', defaultValue: null),
        new ParameterDef(name: 'myParamB'),
    ],
);

// Would output
class MyClass
{
    public function __construct(
        public $myPropA = null,
        $myParamB,
    ) {
    }
}
```

# Instructions

Instructions are themselves a very feature-rich topic. The whole subject is addressed under another section. Instructions are passed as an array of different objects into the `instructions` parameter. See the documentation to understand how `instructions` are processed and converted.

- [Instructions](../../Instructions.md)