# Class traits

Classes can declare trait usage. For now, `UseTraitDef` only supports single class imports and thus does not support conflict resolution.

# Adding a trait to a class

When adding a trait to a class, you can pass the `traits` array any of the following:

- `string`
- `ClassTypeDef`
- `UseTraitDef`

All will be converted to a `UseTraitDef` in the end:

```php
$myClass = new ClassDef(
    name: 'MyClass',
    traits: [
        'Foo\Bar\TraitA',
        new ClassTypeDef('Foo\Bar\TraitB')
        new UseTraitDef('Foo\Bar\TraitC')
    ],
);

// Would output
class MyClass
{
    use Foo\Bar\TraitA;
    use Foo\Bar\TraitB;
    use Foo\Bar\TraitC;
}
```

# Future support

- Multiple imported traits in the same statement
- Conflict resolution
- Alias & Visibility resolution