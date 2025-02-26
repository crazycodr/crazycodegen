To create classes or methods you can use multipart constructions like so:

```php
$df = new DefinitionsFactory();
$xf = new ExpressionsFactory();
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