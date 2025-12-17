# ClassDef: Defining a class

Class definition is the most feature-rich building block in the framework. It defines a complete PHP class, including namespace, inheritance, interfaces, imports, modifiers, docblock, and more.

Quick access:

- [Basic class definition](#basic-class-definition)
- [Setting up a namespace](#setting-up-a-namespace)
- [Extending another class](#extending-another-class)
- [Implementations](#implementations)
- [Imports](#imports)
- [DocBlocks](#docblocks)
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

Using the `namespace` parameter, one can create the class under a specific namespace. Namespaces are defined at the class level because PHP standards state only one class should be present per file. To assign a namespace to a class, you must pass in a `NamespaceDef` object:

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

# Extending another class

Class definitions also feature an `extends` parameter in which you must supply a `ClassTypeDef` object. You can obtain a `ClassTypeDef` from another `ClassDef` you created by calling `$otherClass->getClassType()`:

> Note that the example below has `imports` using both `ClassTypeDef` or `ImportDef` to show that it will shorten the identifier in the output code. More on this below.

```php
$baseClass = new ClassDef(name: 'Foo\Bar\BaseClass');
$myClass = new ClassDef(
    name: 'MyClass',
    extends: new ClassTypeDef('Foo\Bar\BaseClass'),
    imports: [new ImportDef('Foo\Bar\BaseClass')]
);
$myClass2 = new ClassDef(
    name: 'MyClass2',
    extends: $baseClass->getClassType(),
    imports: [$baseClass->getClassType()]
);

// Would output
use BaseClass;

class MyClass extends BaseClass
{
}
```

# Implementations

If your class needs to implement interfaces, you must pass an array of `ClassTypeDef` objects.

> Note that the framework does not allow the generation of interfaces yet but once it is in, you will be able to use interfaces the same way by calling `$interface->getClassType()`.

```php
$baseInt1 = new ClassTypeDef('Foo\Bar\BaseInterface1');
$baseInt2 = new ClassTypeDef('Foo\Bar\BaseInterface2');
$myClass = new ClassDef(
    name: 'MyClass',
    implementations: [$baseInt1, $baseInt2],
);

// Would output
use Foo\Bar\BaseInterface1;
use Foo\Bar\BaseInterface2;

class MyClass implements BaseInterface1, BaseInterface2
{
}
```

# Imports

Imports add `use` statements above your class to import. Imports are defined at the class level because PHP standards state only one class should be present per file. The library can simplify many class/interface references in your code by importing them into the current file. To simplify the code's output, you must add those imports to the `ClassDef` using the `imports` parameter as follows, passing an array of `ClassTypeDef` objects:

```php
$baseInt1 = new ClassTypeDef('Foo\Bar\BaseInterface1');
$baseInt2 = new ClassTypeDef('Foo\Bar\BaseInterface2');
$myClass = new ClassDef(
    name: 'MyClass',
    imports: [$baseInt1, $baseInt2],
    implementations: [$baseInt1, $baseInt2],
);

// Would output
use Foo\Bar\BaseInterface1;
use Foo\Bar\BaseInterface2;

class MyClass implements BaseInterface1, BaseInterface2
{
}
```

# DocBlocks

You can add a `DocBlockDef` to describe the class:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    docBlock: new DocBlockDef([
        'A single line of text to be added before the class in the DocBlock.'
    ]),
);

// Would output
/**
 * A single line of text to be added before the class in the DocBlock.
 */
class MyClass
{
}
```

> `DocBlockDef` will eventually support annotations by adding definitions to it such as parameters, return types, exceptions. For now, you have to generate these parts yourself.

# Modifiers

For now, only the `abstract` modifier is supported for `ClassDef`. Support for `final` and `readonly` modifiers will be added in a later release:

```php
$myClass = new ClassDef(name: 'MyClass', abstract: true);

// Would output
abstract class MyClass
{
}
```

# Properties and methods

Constants, properties and methods are a complex subject that each require their own page:

- [Class traits](ClassTraits.md)
- [Class constants](ClassConstants.md)
- [Class properties](ClassProperties.md)
- [Class methods](ClassMethods.md)