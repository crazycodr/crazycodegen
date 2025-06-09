<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Values;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Definitions\Values\BoolVal;
use CrazyCodeGen\Definition\Definitions\Values\FloatVal;
use CrazyCodeGen\Definition\Definitions\Values\IntVal;
use CrazyCodeGen\Definition\Definitions\Values\NullVal;
use CrazyCodeGen\Definition\Definitions\Values\StringVal;
use CrazyCodeGen\Definition\Definitions\Values\ValueInferenceTrait;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;
use PHPUnit\Framework\TestCase;

class ValueInferenceTraitTest extends TestCase
{
    use TokenFunctions;
    use ValueInferenceTrait;

    public function testValueCanOrCannotBeProperlyInferredOn()
    {
        $this->assertTrue($this->isInferableValue(null));
        $this->assertTrue($this->isInferableValue(1));
        $this->assertTrue($this->isInferableValue(2.985));
        $this->assertTrue($this->isInferableValue(true));
        $this->assertTrue($this->isInferableValue(false));
        $this->assertTrue($this->isInferableValue('some string'));
        $this->assertTrue($this->isInferableValue(['an array' => 'of values']));

        $this->assertFalse($this->isInferableValue(new Expression('Cannot understand what an object is')));
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function testValueIsInferredProperly()
    {
        $this->assertInstanceOf(NullVal::class, $this->inferValue(null));
        $this->assertInstanceOf(IntVal::class, $this->inferValue(1));
        $this->assertInstanceOf(FloatVal::class, $this->inferValue(2.985));
        $this->assertInstanceOf(BoolVal::class, $this->inferValue(true));
        $this->assertInstanceOf(BoolVal::class, $this->inferValue(false));
        $this->assertInstanceOf(StringVal::class, $this->inferValue('some string'));
        $this->assertInstanceOf(ArrayVal::class, $this->inferValue(['an array' => 'of values']));

        $this->assertInstanceOf(NullVal::class, $this->inferValue(new Expression('Cannot understand what an object is so it nullifies it')));
    }
}
