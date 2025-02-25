<?php

namespace CrazyCodeGen\Traits;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Definitions\Values\BoolValue;
use CrazyCodeGen\Definitions\Values\FloatValue;
use CrazyCodeGen\Definitions\Values\IntValue;
use CrazyCodeGen\Definitions\Values\StringValue;
use CrazyCodeGen\Exceptions\NonComputableValueException;

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
            return new StringValue($value);
        }
        throw new NonComputableValueException();
    }
}