The system converts any literal to its appropriate type without having to expressly do so. The following are equivalent:

```php
$one = $df->intVal(1);
$one = 1;
```

You can use `$one` anywhere, and it will still resolve to the constant `1` in the code. The only moment where you 
really want to use type inference like that is when you have special cases such as:

```php
$true = $df->boolVal('true');
$stringTrue = 'true';
```

Now, both are not the same.

To create classes or methods you can use multipart constructions like so:

```php
$df = new DefinitionsFactory();
$xf = new ExpressionsFactory();
$df->override('classDef', CrazyCodrGen\ClassDef::class);
$class = $df->classDef(name: 'MyClass');
$class->addProperty($bar = $df->property(name: 'bar', type: $df->mixed(), default: 'bar'));
$class->addMethod($method = $df->getMethodDef(
    name: 'helloWorld',
    args: [
        $foo => $df->argument(name: 'foo', type: $df->static())    
    ],
    returns: $df->static()
));
$method->addBody([
    $xf->exp($xf->assign($bar, $foo)),
    $xf->exp($xf->returns($df->this())),
])
```

Will yield:

```php
class MyClass
{
    public mixed $bar = 'bar';
    
    public function helloWorld(static $foo): static
    {
        $this->bar = $foo;
        return $this;
    }
}
```

In the context, when rendering the code, the code sees `$bar` in `assign(...)` but because it is a variable and has 
a reference to its context, it will automatically infer `$this` and result in a shorthand of:

```php
$xf->exp($xf->assign($xf->access($xf->this(), $bar), $foo));
```

It is actually better to let the inference be run because later if you use `$bar` from another context, it will use the
variable instead. For example:

```php
$target = $df->variable(name: 'target');
$instance = $df->variable(name: 'instance');
$xf->assign($target, $bar->of($instance));

// Will generate
$target = $instance->bar; 
```

To generate expressions, you can use the expression builder which allows easier chaining of constructions while still
allowing for multipart constructed expressions:

```php
// The following
$target = $df->variable(name: 'target');
$instance = $df->variable(name: 'instance');
$one = $df->intVal(1);
$two = $df->intVal(2);
$addition = $xf->add($one, $bar->of($instance));
$wrappedAddition = $xf->wrap($addition);
$multiplication = $dx->mul($wrappedAddition, $two);
$assign = $xf->assign($target, $multiplication);

// Can be condensed to
$target = $df->variable(name: 'target');
$instance = $df->variable(name: 'instance');
$assign = $xf->assign(
    $target, 
    $dx->mul(
        $xf->wraps(
            xf->adds(
                $df->intVal(1), 
                $bar->of($instance)
            )
        ), 
        $df->intVal(2)
    )
);

// But is also the same as
$target = $df->variable(name: 'target');
$instance = $df->variable(name: 'instance');
$assign = $xf->assign($target, $xf->build('(', 1, '+', $bar->of($instance), ')'));
```

The last version with `build` actually considers all operators such as `()+-*/%?:=` to be tokens that must be 
converted to their operator equivalents as if you specified them in a multipart construction. The build takes
order of operators into account to match the different left, right and third operators for you.

```php
$xf->wraps(...) // Needs to find a corresponding start and end parenthesis
$xf->equals(...) // Needs a left and right operand
$xf->adds(...) // Needs a left and right operand
$xf->subs(...) // Needs a left and right operand
```
Therefore, in such a scenario, based on the rules of PHP, the following build patterns yield the following exploded 
multipart code builds:

```php
$xf->build('(', 1, '+', 2, '*', 3, ')')
$xf->wraps($xf->adds($df->intVal(1), $xf->mults($df->intVal(2), $df->intVal(3))));

$xf->build('(', 1, '+', 2, ')', '*', 3)
$xf->mults($xf->wraps($xf->adds($df->intVal(1), $df->intVal(2))), $df->intVal(3));
```

There are 3 types of operators:

- Unary
- Binary
- Ternary

```php
// Unary operator
$xf->notNot(true);
!!true

// Binary operator
$xf->ands(true, false);
true && false

$xf->ors(true, false);
true || false

$xf->bands(2, 1);
2 & 1

$xf->xors(2, 1);
2 xor 1

$xf->equals(2, 1);
2 === 1

$xf->equals(2, 1, soft: true);
2 == 1

// Ternary operator
$xf->ternIf($xf->equals($foo, 1), 'yes', 'no');
$foo === 1 ? 'yes' : 'no'

$xf->ternIf($xf->build('use', '==', 1), 'really?', 'impossible')
'use' == 1 ? 'really?' : 'impossible'
```

To render code, you first need a renderer. Renderers are fully customizable so you can properly render your code the
way you wish it to be returned. You can even send in custom rules that evaluate how to interpret the configuration
based on context:

```php
$exp = $xf->ternIf($xf->equals($foo, 1), 'yes', 'no');

$renderer = new Renderer(rules: PSR2RenderingRules::getRules());
$renderer->render($exp);

$foo === 1 ? 'yes' : 'no'
```

The rules are varied, we'll add more rules as we go to enable more configuration.