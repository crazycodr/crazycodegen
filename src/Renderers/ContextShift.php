<?php

namespace CrazyCodeGen\Renderers;

class ContextShift
{
    public function __construct(
        public ContextShiftOperationEnum $shiftOperation,
        public ContextTypeEnum           $shiftedType
    )
    {
    }

    public static function push(ContextTypeEnum $shiftedType): ContextShift
    {
        return new self(ContextShiftOperationEnum::push, $shiftedType);
    }

    public static function pop(ContextTypeEnum $shiftedType): ContextShift
    {
        return new self(ContextShiftOperationEnum::pop, $shiftedType);
    }
}