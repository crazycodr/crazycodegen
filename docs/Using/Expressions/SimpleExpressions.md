# Simple expressions and rendering rules

The framework allows you to set up very simple to very complex expressions. Your expressions can be defined as a string or as a series of expression objects.

## Using strings

You can pass a raw string to `Expression`, which will treat the entire string as a single, unparsed token. This can be convenient for quick expressions:

```php
$expression = new Expression('1 + 2 * (6 - 3) / 4');
```

While valid, this bypasses the framework's token-based parsing. As a result, **automatic formatting (like line wrapping, spacing, and indentation) won't apply**. This may cause unexpected output when using strict rendering rules:

```php
$rules = new RenderingRules(lineLength: 10);
$renderer = new Renderer($rules);
echo $renderer->render($expression);
```

**Actual output:**
```php
1 + 2 * (6 - 3) / 4
```

**Expected output with proper tokenization:**
```php
1 + 2 * (
    6 - 3
) / 4
```

To benefit from auto-formatting, consider using expression trees of objects instead of raw strings.

## Using expression trees

A more robust approach is to build expressions using object trees. This creates an in-memory structure similar to an Abstract Syntax Tree (AST). While CrazyCodeGen doesn't follow a strict AST model, its design is very close. The key advantage of this approach is full compatibility with the framework's rich and configurable rendering rules.

```php
$rules = new RenderingRules(arrays: new ArrayRules(wrap: WrappingDecision::IF_TOO_LONG));
$renderer = new Renderer($rules);
$expression = new ArrayVal([1 => 'hello world', 2 => 'foo-bar-baz', 3 => 'I love computers']);
echo $renderer->render($expression);
```

**Actual output:**
```php
[1 => 'hello world', 2 => 'foo-bar-baz', 3 => 'I love computers']
```

**Change the line length**
```php
$rules = new RenderingRules(
    lineLength: 25,
    arrays: new ArrayRules(wrap: WrappingDecision::IF_TOO_LONG)
);
[
    1 => 'hello world',
    2 => 'foo-bar-baz',
    3 => 'I love computers'
]
```

**Same line length but force the wrapping decision to always wrap or never wrap**
```php
$rules = new RenderingRules(arrays: new ArrayRules(wrap: WrappingDecision::ALWAYS));
[
    1 => 'hello world',
    2 => 'foo-bar-baz',
    3 => 'I love computers'
]

$rules = new RenderingRules(
    lineLength: 25,
    arrays: new ArrayRules(wrap: WrappingDecision::NEVER)
);
[1 => 'hello world', 2 => 'foo-bar-baz', 3 => 'I love computers']
```
