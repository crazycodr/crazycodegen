<?php

namespace CrazyCodeGen\Renderers;

class RenderContext
{
    public function __construct(
        public string $buffer = '',
        public string $indents = '',
        /** @var ContextTypeEnum[] $contextStack */
        public array  $contextStack = [],
    )
    {
    }

    public function applyContextShift(ContextShift $contextShift): void
    {
        if ($contextShift->shiftOperation === ContextShiftOperationEnum::push) {
            $this->contextStack[] = $contextShift->shiftedType;
        } else {
            $poppedType = array_pop($this->contextStack);
            if ($poppedType !== $contextShift->shiftedType) {
                $this->contextStack[] = $poppedType;
            }
        }
    }

    public function is(ContextTypeEnum $seekedContext): bool
    {
        return $this->contextStack[count($this->contextStack) - 1] === $seekedContext;
    }

    public function has(ContextTypeEnum $seekedContext): bool
    {
        return in_array($seekedContext, $this->contextStack);
    }

    public function getCurrent(): null|ContextTypeEnum
    {
        return $this->contextStack[count($this->contextStack) - 1] ?? null;
    }
}