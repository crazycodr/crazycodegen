<?php

namespace CrazyCodeGen\Formatting;

class PrintResultLineGroup
{
    /** @var string[] $lines */
    public array $lines = [];

    public function __construct(
        /** @var string[] $lines */
        array $lines = [],
    ) {
        $this->addLines(...$lines);
    }

    public function addLines(string ...$lines): void
    {
        $this->lines = array_merge($this->lines, $lines);
    }

    public function clone(): self
    {
        return new self($this->lines);
    }

    public function indentAllLines(PrintingContext $context, bool $exceptFirst = false): void
    {
        foreach ($this->lines as $lineIndex => $line) {
            if ($lineIndex === 0 && $exceptFirst) {
                continue;
            }
            $this->lines[$lineIndex] = $context->getIndentation() . $line;
        }
    }
}
