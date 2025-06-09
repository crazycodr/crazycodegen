# Value definitions

The framework supports many types of values, allowing you to represent values like integers (`1`, `2`, `3`), strings (`"Foo"`), and more complex structures. There are many types of value you can define, here are the basic ones:

- BoolVal: Stores a boolean
- FloatVal: Stores a float
- IntVal: Stores an integer
- NullVal: Stores a null
- StringVal: Stores a string

You can also use the more complex array one:

- ArrayVal: Stores an array of values

And then there is also special values such as:

- ClassRefVal: Stores a reference to a class type

## Using scalar value definitions

Scalar value definitions are very simple to use:

```php
$myBool = new BoolVal(true);
$myInt = new IntVal(123);
$myFloat = new FloatVal(123.456);
$myString = new StringVal("hello world");
$myNull = new NullVal();
```

Each value definition accepts only its corresponding native type. However, PHP may coerce compatible values automatically, which helps avoid type errors in simple cases.

## Using array values

A very important type in PHP is the array type. `ArrayVal` allows you to specify direct values under it and these will be converted to the above scalar types as needed.

```php
$array = new ArrayVal('foo', 'bar', 'baz');

// Would output something like this
['foo', 'bar', 'baz']
```

The constructor accepts a variable number of scalar or value arguments. For associative arrays, you can use an array of key-value pairs. If there are no keys or if keys are in numerical order, the framework will not output keys:

```php
$array = new ArrayVal(['key' => new StringVal('value')]);

// Would output to
['key' => 'value']

$array = new ArrayVal([0 => 'foo', 1 => 'bar', 2 => 'baz'])

// Would output to key less form because all keys are in numerical order
['foo', 'bar', 'baz']
```

> **Note:** Keys must be actual integers to be considered sequential. If numeric keys are provided as strings (e.g., `'0'`, `'1'`), they will not be treated as sequential, and the keys will be rendered explicitly.

If you need to nest arrays inside of arrays, you can do it easily. Depending on the rendering rules configuration you can get the arrays to chop down and properly indent the code:

```php
$array = new ArrayVal('foo' => new ArrayVal('bar', 'baz'));

// Would output something like this
['foo' => ['bar', 'baz']]

// Or this if it decides to chop down due to length
[
    'foo' => [
        'bar',
        'baz',
    ],
]
```

## The `ClassRefVal`

The `ClassRefVal` is a value object that represents a type. If you pass in a type in itself as a value, it would render as `Name\Space\To\Type` or be shortened to just `Type` but the reality is it needs to be represented as `\Name\Space\To\Type::class` or `Type::class` using PHP’s `::class` constant. This is what the `ClassRefVal` value type is for, it is to represent a reference to a type using the class keyword.

In most cases, you won't need to construct a `ClassRefVal` manually — related parts of the framework will automatically convert strings or `ClassDefType` objects into `ClassRefVal` when appropriate:

```php
$type = new ClassDefType('Name\\Space\\To\\Type');
$array = new ArrayVal(['class' => $type]);

// This will convert automatically to a ClassRefVal instead
['class' => Type::class]
```

## Other objects that act as references

There are other objects that act as references such as variables, properties, constants, etc. These reference objects will be introduced in later sections. When used in expressions, they automatically render according to their context — for example, as property access or variable references.

```php
$property = new PropertyDef('prop');
$array = new ArrayVal($property);

// This will render as
[$this->prop]
```

## Value inference trait

There is a trait available inside the same namespace named `ValueInferenceTrait`. This should be the one approach used everywhere to convert literal or natural values provided to expressions or other components.

To use it, import it into your code and call the method, it will return the most appropriate value object:

```php
class YourComponent {
    use ValueInferenceTrait;
    
    public function __construct(mixed $value)
    {
    }
    
    public function getTokens(): array {
        return $this->inferValue($this->value);
    }
}
```