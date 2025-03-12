<?php

namespace CrazyCodeGen\Definition\Traits;

use CrazyCodeGen\Definition\Base\CanBeComputed;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Definitions\Values\BoolVal;
use CrazyCodeGen\Definition\Definitions\Values\BoolValue;
use CrazyCodeGen\Definition\Definitions\Values\FloatVal;
use CrazyCodeGen\Definition\Definitions\Values\FloatValue;
use CrazyCodeGen\Definition\Definitions\Values\IntVal;
use CrazyCodeGen\Definition\Definitions\Values\IntValue;
use CrazyCodeGen\Definition\Definitions\Values\NullVal;
use CrazyCodeGen\Definition\Definitions\Values\NullValue;
use CrazyCodeGen\Definition\Definitions\Values\OldStringValue;
use CrazyCodeGen\Definition\Definitions\Values\StringVal;
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

    public function isScalarType(mixed $value): bool
    {
        return is_int($value)
            || is_float($value)
            || is_string($value)
            || is_bool($value)
            || is_null($value);
    }

    /**
     * @throws NonComputableValueException
     */
    public function getValOrReturn(mixed $value): Tokenizes
    {
        if ($value instanceof Tokenizes) {
            return $value;
        }
        if (is_float($value)) {
            return new FloatVal($value);
        }
        if (is_int($value)) {
            return new IntVal($value);
        }
        if (is_bool($value)) {
            return new BoolVal($value);
        }
        if (is_string($value)) {
            return new StringVal($value);
        }
        if ($value === null) {
            return new NullVal();
        }
        throw new NonComputableValueException();
    }
}
