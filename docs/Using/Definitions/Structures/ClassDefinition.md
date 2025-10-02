# ClassDef: Defining a class

`ClassDef` is the most feature-rich building block in the framework. It defines a complete PHP class, including namespace, inheritance, interfaces, imports, modifiers, docblock, and more.

Quick access:

- [Basic class definition](#basic-class-definition)
- [Setting up a namespace](#setting-up-a-namespace)
- [Extending another class](#extending-another-class)
- [Implementations](#implementations)
- [Imports](#imports)
- [DocBlocks](#doc-blocks)
- [Modifiers](#modifiers)
- [Properties and methods](#properties-and-methods)

# Basic class definition

To define a new class, you have to instantiate a `ClassDef` with at least one argument, the `name`. The name must follow the PHP class identifier naming conventions and must not contain the namespace: (See below for namespace definition)

```php
$myClass = new ClassDef(name: 'MyClass');

// Would output
class MyClass
{
}
```

# Setting up a namespace

Using the `namespace` parameter, one can create the class under a specific namespace. You must pass in a `NamespaceDef` object:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    namespace: new NamespaceDef('Foo\Bar'),
);

// Would output
namespace Foo\Bar;

class MyClass
{
}
```

Alternatively, you can also create a namespace object that you will be reusing. You can create multiple `NamespaceDef` instances with the same path. These are not automatically deduplicated or shared, and they are treated as distinct objects without special linking behavior. Having multiple `NamespaceDef` objects for the same namespace should not have any impact on code generation.

```php
$fooBarNs = new NamespaceDef(path: 'Foo\\Bar');
$myClass = new ClassDef(name: 'MyClass', namespace: $fooBarNs);

// Would output
namespace Foo\Bar;

class MyClass
{
}
```

# Extending another class

Class definitions also feature a `extends` parameter in which you can supply a simple string such as `Foo\\Bar\\BaseClass` or you can pass it a `ClassTypeDef` of another `ClassDef` which you can obtain using `->getClassType()`:

```php
$myClass = new ClassDef(name: 'MyClass', extends: 'Foo\\Bar\\BaseClass');

// Would output
use Foo\Bar\BaseClass;

class MyClass extends BaseClass
{
}
```

When using another `ClassDef` you can do it like so:

```php
$baseClass = new ClassDef(name: 'BaseClass');
$myClass = new ClassDef(name: 'MyClass', extends: $baseClass->getClassType());

// Would output
use Foo\Bar\BaseClass;

class MyClass extends BaseClass
{
}
```

# Implementations

If your class needs to implement an interface, you can use pretty much the same mechanics as extension except here you can pass in multiple `ClassTypeDef` or strings:

```php
$baseInt2 = new ClassTypeDef('Foo\\Bar\\BaseInterface2');
$myClass = new ClassDef(
    name: 'MyClass',
    implementations: [
        'Foo\\Bar\\BaseInterface1',
        $baseInt2,
    ],
);

// Would output
use Foo\Bar\BaseInterface1;
use Foo\Bar\BaseInterface2;

class MyClass implements BaseInterface1, BaseInterface2
{
}
```

# Imports

PHP can simplify many class/interface references in your code by importing them into the current file. The examples depicted above assume that you actually added the imports. If we review the above example, what would really happen is the following if you did not add imports to the `ClassDef`:

```php
$baseInt2 = new ClassTypeDef('Foo\\Bar\\BaseInterface2');
$myClass = new ClassDef(
    name: 'MyClass',
    implementations: [
        'Foo\\Bar\\BaseInterface1',
        $baseInt2,
    ],
);

// Would output
class MyClass implements Foo\Bar\BaseInterface1, Foo\Bar\BaseInterface2
{
}
```

To simplify the code's output, you must add those imports to the `ClassDef` using the `imports` parameter as follows:

```php
$baseInt2 = new ClassTypeDef('Foo\\Bar\\BaseInterface2');
$myClass = new ClassDef(
    name: 'MyClass',
    imports: [
        'Foo\\Bar\\BaseInterface1',
        $baseInt2,
    ],
    implementations: [
        'Foo\\Bar\\BaseInterface1',
        $baseInt2,
    ],
);

// Would output
use Foo\Bar\BaseInterface1;
use Foo\Bar\BaseInterface2;

class MyClass implements Foo\Bar\BaseInterface1, Foo\Bar\BaseInterface2
{
}
```

And this is now where `ClassTypeDef` improves code clarity by enabling reusable, strongly-typed references:

```php
$baseInt1 = new ClassTypeDef('Foo\\Bar\\BaseInterface1');
$baseInt2 = new ClassTypeDef('Foo\\Bar\\BaseInterface2');
$myClass = new ClassDef(
    name: 'MyClass',
    imports: [$baseInt1, $baseInt2],
    implementations: [$baseInt1, $baseInt2],
);

// Would output
use Foo\Bar\BaseInterface1;
use Foo\Bar\BaseInterface2;

class MyClass implements Foo\Bar\BaseInterface1, Foo\Bar\BaseInterface2
{
}
```

# DocBlocks

You can add a DocBlock to describe the class. To do so, you can pass a single `string`, a `string[]` or a `DocBlockDef` object. When you pass a `string` or `string[]`, the framework automatically wraps it in a `DocBlockDef` for you. Internally, this always produces the same result as writing a full `DocBlockDef` manually:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    docBlock: 'A single line of text to be added before the class in the DocBlock.',
);

// Would output
/**
 * A single line of text to be added before the class in the DocBlock.
 */
class MyClass
{
}
```

Multiple lines of text through an array (`string[]`) would just create a `DocBlockDef` with multiple text lines:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    docBlock: [
        'A single line of text to be added before the class in the DocBlock.',
        'This file is generated, do not override it.'
    ],
);

// Would output
/**
 * A single line of text to be added before the class in the DocBlock.
 * This file is generated, do not override it.
 */
class MyClass
{
}
```

# Modifiers

For now, only the `abstract` modifier is supported for `ClassDef`:

```php
$myClass = new ClassDef(name: 'MyClass', abstract: true);

// Would output
abstract class MyClass
{
}
```

> Support for `final` and `readonly` modifiers will be added in a later release.

# Properties and methods

Because class properties and methods involve many parameters and behaviors, they are covered on separate pages:

- [Class properties](ClassProperties.md)
- [Class methods](ClassMethods.md)