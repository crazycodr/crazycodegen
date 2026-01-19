<?php

namespace CrazyCodeGen\Formatting;

use CrazyCodeGen\Formatting\Enums\VerticalLayoutStrategyEnum;

class PrintingContext
{
    private string $indentation = '';

    public function __construct(
        public string                     $indentationChar = ' ',
        public int                        $indentationSize = 4,
        public int                        $indentationLevel = 0,
        public int                        $inlineSpaceAvailable = 0,
        public int                        $newLineSpaceAvailable = 0,
        public bool                       $isVertical = false,
        public bool                       $requiresVerticalFromParent = false,
        public VerticalLayoutStrategyEnum $verticalLayoutStrategy = VerticalLayoutStrategyEnum::Avoid,
    ) {
        $this->updateIndentation();
    }

    public function indent(): void
    {
        $this->indentationLevel++;
        $this->updateIndentation();
        $this->newLineSpaceAvailable -= $this->indentationSize;
    }

    public function outdent(): void
    {
        if ($this->indentationLevel > 0) {
            $this->indentationLevel--;
            $this->updateIndentation();
            $this->newLineSpaceAvailable += $this->indentationSize;
        }
    }

    public function getIndentation(): string
    {
        return $this->indentation;
    }

    private function updateIndentation(): void
    {
        $this->indentation = str_repeat(
            $this->indentationChar,
            $this->indentationLevel * $this->indentationSize,
        );
    }

    public function clone(
        null|int                        $indentationLevel = null,
        null|int                        $inlineSpaceAvailable = null,
        null|int                        $newLineSpaceAvailable = null,
        null|bool                       $isVertical = null,
        null|VerticalLayoutStrategyEnum $dropDownStrategy = null,
    ): PrintingContext
    {
        return new PrintingContext(
            indentationChar: $this->indentationChar,
            indentationSize: $this->indentationSize,
            indentationLevel: $indentationLevel ?? $this->indentationLevel,
            inlineSpaceAvailable: $inlineSpaceAvailable ?? $this->inlineSpaceAvailable,
            newLineSpaceAvailable: $newLineSpaceAvailable ?? $this->newLineSpaceAvailable,
            isVertical:  $isVertical ?? $this->isVertical,
            requiresVerticalFromParent: $this->requiresVerticalFromParent,
            verticalLayoutStrategy: $dropDownStrategy ?? $this->verticalLayoutStrategy,
        );
    }

    public function fitsInline(string $line): bool
    {
        return mb_strlen($line) <= $this->inlineSpaceAvailable;
    }

    public function fitsInNewline(string $line): bool
    {
        return strlen($line) <= $this->newLineSpaceAvailable;
    }
}
