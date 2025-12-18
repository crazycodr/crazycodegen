# Class properties

Classes usually feature data in the form of properties. To define class properties, you must use the `PropertyDef` class and add them to the `properties` array of the `ClassDef` object you are defining.

# Naming your property

When creating a `PropertyDef`, you must set a `name` that respects PHP identifier practices. You must not add the leading `$` sign, this will be done by the framework:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA'),
        new PropertyDef(name: 'myPropB'),
    ],
);

// Would output
class MyClass
{
    public $myPropA;
    public $myPropB;
}
```

# Changing the visibility

Class properties are by default `public`. You can change the `visibility` by passing one of the different `VisibilityEnum` values:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA', visibility: VisibilityEnum::PROTECTED),
        new PropertyDef(name: 'myPropB', visibility: VisibilityEnum::PRIVATE),    
    ],
);

// Would output
class MyClass
{
    protected $myPropA;
    private $myPropB;
}
```

# Modifiers

You can set different modifiers on class properties such as `static` and `readonly`. The framework **does not prevent invalid mix of modifiers with other properties**. For example, you cannot mix `static` and `readonly` nor can you `readonly` with a `defaultValue` unless it is a constructor promoted property.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA', static: true),
        new PropertyDef(name: 'myPropB', readOnly: true),    
    ],
);

// Would output
class MyClass
{
    public static $myPropA;
    public readonly $myPropB;
}
```

# Types

Class properties can and should be typed for best-practice reasons. You can pass a `TypeDef` object such as `BuiltInTypeSpec::intType()` or a `ClassTypeDef` object that you generate: (See [TypeDefinitions.md](../TypeDefinitions.md) for more information)

> Note that the example below does not feature `imports` but if you did, using `ClassTypeDef` in the return type, it would shorten the identifier in the output code.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA', type: new ClassTypeDef('Foo\Bar\Baz')),
        new PropertyDef(name: 'myPropB', type: BuiltInTypeSpec::intType()),
    ],
);

// Would output
class MyClass
{
    public Foo\Bar\Baz $myPropA;
    public int $myPropB;
}
```

# DocBlocks

You can add a `DocBlockDef` to describe the property. This is especially important when using types that are of the `array` type so that IDEs can guide the developer with what is acceptable.

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(
            name: 'myPropA',
            docBlock: new DocBlockDef([
                'Contains the "A" data',
                '@var string[]',
            ]),
            type: BuiltInTypeSpec::arrayType()
        ),    
    ],
);

// Would output
class MyClass
{
    /**
     * Contains the "A" data. 
     * @var string[] 
     */
    public array $myPropA;
}
```

# Default values

Sometimes, a property will feature a default value. To set one, just pass anything to `defaultValue` and the default value will be transformed into a valid expression. The `defaultValue` is not `null` by default, it is `@!#UNSET@!#` so that the framework can detect that you want to set a `null` default. Therefore, the only impossible default value to set on a property using this framework is `@!#UNSET@!#`.

Also note that the value is typed as `mixed` but it actually only accepts valid inferable values from `ValueInferenceTrait::inferValue`. (See [Value definitions](../ValueDefinitions.md)).

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA', defaultValue: null),
        new PropertyDef(name: 'myPropB', defaultValue: ['foo' => 'bar']),
    ],
);

// Would output
class MyClass
{
    public $myPropA = null;
    public $myPropB = ['foo' => 'bar'];
}
```