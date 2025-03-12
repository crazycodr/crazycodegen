<?php

namespace CrazyCodeGen\Definition\Traits;

use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Definitions\Values\BoolValue;
use CrazyCodeGen\Definition\Definitions\Values\FloatValue;
use CrazyCodeGen\Definition\Definitions\Values\IntValue;
use CrazyCodeGen\Definition\Definitions\Values\NullValue;
use CrazyCodeGen\Definition\Definitions\Values\OldStringValue;
use CrazyCodeGen\Definition\Exceptions\NonComputableValueException;

trait ComputableTrait
{
    /**
     * @throws NonComputableValueException
     */
    public function makeComputed(mixed $value): CanBeComputed
    {
        if ($value instanceof CanBeComputed) {
            return $value;
        }
        if (is_float($value)) {
            return new FloatValue($value);
        }
        if (is_int($value)) {
            return new IntValue($value);
        }
        if (is_bool($value)) {
            return new BoolValue($value);
        }
        if (is_string($value)) {
            return new OldStringValue($value);
        }
        if ($value === null) {
            return new NullValue();
        }
        throw new NonComputableValueException();
    }
}
