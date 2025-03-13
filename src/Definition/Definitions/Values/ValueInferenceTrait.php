<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

trait ValueInferenceTrait
{
    public function isInferableValue(mixed $value): bool
    {
        if (is_int($value)) {
            return true;
        } elseif (is_float($value)) {
            return true;
        } elseif (is_bool($value)) {
            return true;
        } elseif (is_string($value)) {
            return true;
        } elseif (is_array($value)) {
            return true;
        } elseif (is_null($value)) {
            return true;
        } else {
            return false;
        }
    }

    public function inferValue(mixed $value): BaseVal
    {
        if (is_int($value)) {
            return new IntVal($value);
        } elseif (is_float($value)) {
            return new FloatVal($value);
        } elseif (is_bool($value)) {
            return new BoolVal($value);
        } elseif (is_string($value)) {
            return new StringVal($value);
        } elseif (is_array($value)) {
            return new ArrayVal($value);
        } elseif (is_null($value)) {
            return new NullVal();
        } else {
            return new NullVal();
        }
    }
}
