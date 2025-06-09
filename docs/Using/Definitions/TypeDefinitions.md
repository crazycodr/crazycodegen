# Type definitions

As you will see in the next section, the whole point of the framework is to be able to generate complex expressions, classes and even methods. To this end, generating classes do generate types in an implicit way but sometimes, you won't be defining all the possible types your application has using class structures. This is why we will start with a simpler concept called type definitions.

# Types of type definition

There are officially 2 important type definitions you can use in the framework.

## BuiltInTypeSpec

The `BuiltInTypeSpec` represents all built in types from PHP such as: 

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

When you need to specify a type that is handled by PHP's internals use `BuildInTypeSpec`:

```php
$int = new BuiltInTypeSpec('int');
$propA = new PropertyDef(type: $int);
$propB = new PropertyDef(type: new BuiltInTypeSpec('string'));
```

## ClassTypeDef

The `ClassTypeDef` is the opposite of the built-in one where it is used to represent any non internal type which are automatically classes. These classes could be provided by:

- PHP itself: For example: `splFileInfo`
- A vendor package: For example `TestCase`  from PHPUnit
- Your application: `\App\MyClass`

Therefore, any class type that you want to express while building new classes with the code generator should use `ClassTypeDef` unless it is a type that you are actively generating.

```php
$redisClient = new ClassTypeDef('Redis');
$myRedisWrapper = new ClassDef(extends: $redisClient);
$myRedisConsumer = new ClassDef(
    properties: [
        new PropertyDef(type: $myRedisWrapper),
    ],
);
```

# Other types of type definition

There are other types of type definition that you will need to leverage when using the framework and here they are.

## MultiTypeDef

The `MultiTypeDef` is the type definition that allows you to create complex compound types either for intersection or union. For example:

```php
$nullableInt = new MultiTypeDef([
    new BuiltInTypeSpec('null'),
    new BuiltInTypeSpec('int'),
]);

// This would output
null|int
```

You can also disable union types and force an intersection type by doing so:

```php
$nullableInt = new MultiTypeDef(
    [
        new BuiltInTypeSpec('null'),
        new BuiltInTypeSpec('int'),
    ],
    unionTypes: false,
);

// This would output
null&int
```

You can also nest multiple `MultiTypeDef` together but to do this properly, you need to leverage `nestedTypes`. Using `MultiTypeDef` in this way creates grouped sub-unions that form an intersection — useful for generating parenthesized combinations like (int|string)&(bool|float). Use nestedTypes: true when the inner types themselves are composite.

```php
$nullableInt = new MultiTypeDef(
    [
        new MultiTypeDef(['int', 'string'], nestedTypes: true),
        new MultiTypeDef(['bool', 'float'], nestedTypes: true),
    ],
    unionTypes: false
);

// This would output
(int|string)&(bool|float)
```

> **Note**: The above example is in theory completely impossible as no one could have multiple built-in types like this, but it shows a good example of how to achieve it.

## Context type specifications

Types use either the `Def` or `Spec` suffix: `Spec` types represent language-level references (like self or int), while `Def` types define actual class types that exist in your application or framework.

That being said, this last batch of types expose contexts when referring to something in your code base. Just like built-in types which are references, context types are just references to something else. Here are the 2 contextual types:

- `SelfTypeSpec`: Which represents the `self` type.
- `StaticTypeSpec`: Which represents the `static` type.

Both of these types represent a relative type to another class depending on their context. You should only use these types inside of components that are class scoped. If you use these in a global function definition, you will obviously generate invalid code.

# Type inference trait

The framework offers an easy-to-use type inference trait which can convert strings into types for you. Most components already use this so you don't have to manually instantiate `BuiltInTypeSpec` objects:

```php
public function __construct(string $type)
{
    $this->type = $this->inferType($type);
}

$object = new SomeClass(type: 'int');
```

The inference trait will convert all built-in types to `BuiltInTypeSpec` objects and convert `self` and `static` to their corresponding `SelfTypeSpec` or `StaticTypeSpec`. If nothing matches, it assumes the string is a class type and it returns a `ClassTypeDef`.