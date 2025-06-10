# Class properties

Classes will usually feature data in the form of properties. To define class properties, you must use the `PropertyDef` class and add them to the `properties` array of the `ClassDef` object you are defining.

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

For now, only the `static` modifier exists for class properties:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA', static: true),
        new PropertyDef(name: 'myPropB'),    
    ],
);

// Would output
class MyClass
{
    public static $myPropA;
    public $myPropB;
}
```

> Support for PHP 8.1+ features such as the readonly property is planned in a later release.

# Types

Class properties can and should be typed for best-practice reasons. You can pass a `string` to the `type` property and the type will be inferred automatically into a valid `TypeDef` object:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(name: 'myPropA', type: 'Foo\\Bar\\Baz'),
        new PropertyDef(name: 'myPropB', type: 'int'),    
    ],
);

// Would output
class MyClass
{
    public Foo\Bar\Baz $myPropA;
    public int $myPropB;
}
```

> See [Type definitions](../TypeDefinitions.md) for more information.

# DocBlocks

You can add a DocBlock to describe the property. This is especially important when using `array` as a type so that IDEs can guide the developer with what is acceptable.

To add a docblock, you can pass a single `string`, a `string[]` or a `DocBlockDef` object. When you pass a `string` or `string[]`, the framework automatically wraps it in a `DocBlockDef` for you. Internally, this always produces the same result as writing a full `DocBlockDef` manually:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    properties: [
        new PropertyDef(
            name: 'myPropA',
            docBlock: [
                'Contains the "A" data',
                '@var string[]',
            ],
            type: 'array'
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

Sometimes, a property will feature a default value. To set one, just pass anything to `defaultValue` and the default value will be transformed into a valid expression as much as possible.

The `defaultValue` is not `null` by default, it is `@!#UNSET@!#` so that the framework can detect that you want to set a `null` default. Therefore, the only impossible default value to set on a property using this framework is `@!#UNSET@!#`.

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