<?php

namespace CrazyCodeGen\Formatting;

class PrintResults
{
    /**
     * @var PrintResultLineGroup[]
     */
    public array $lineGroups = [];

    public function __construct(
        public bool  $fitsHorizontally = true,
        /** @var array<PrintResultLineGroup|string> $lineGroups */
        array $lineGroups = [],
    ) {
        foreach ($lineGroups as $lineGroup) {
            if (is_string($lineGroup)) {
                $this->addLines($lineGroup);
            } else {
                $this->addLineGroups($lineGroup);
            }
        }
    }

    public function addLines(string ...$lines): void
    {
        foreach ($lines as $line) {
            $this->addLineGroups(new PrintResultLineGroup([$line]));
        }
    }

    public function addLineGroups(PrintResultLineGroup ...$lineGroups): void
    {
        foreach ($lineGroups as $lineGroup) {
            $this->lineGroups[] = $lineGroup;
        }
    }

    /**
     * @param bool|null $fitsHorizontally
     * @var null|array<PrintResultLineGroup|string> $lineGroups
     * @return self
     */
    public function clone(
        null|bool $fitsHorizontally = null,
        null|array $lineGroups = null,
    ): self {
        $clonedLineGroups = array_map(
            fn (PrintResultLineGroup $lineGroup) => $lineGroup->clone(),
            $lineGroups ?? $this->lineGroups,
        );
        return new self(
            fitsHorizontally: $fitsHorizontally ?? $this->fitsHorizontally,
            lineGroups: $clonedLineGroups,
        );
    }

    /**
     * Flatten will explode all line group lines in the result into single line group lines.
     * @return PrintResults
     */
    public function flatten(): PrintResults
    {
        $result = new PrintResults();
        foreach ($this->lineGroups as $lineGroup) {
            $result->addLines(...$lineGroup->lines);
        }
        return $result;
    }

    /**
     * Takes all lines from all line groups and merges them into a single line group.
     * @return PrintResults
     */
    public function compress(): PrintResults
    {
        $lines = [];
        foreach ($this->lineGroups as $lineGroup) {
            $lines = array_merge($lines, $lineGroup->lines);
        }
        return new PrintResults(lineGroups: [new PrintResultLineGroup($lines)]);
    }

    /**
     * Takes all lines from each line group and merges them last and first lines together to generate an inline result.
     *
     * @return PrintResults
     */
    public function inlineMerge(): PrintResults
    {
        $groupedResult = [];
        foreach ($this->lineGroups as $lineGroup) {
            if (count($lineGroup->lines) === 1) {
                $firstLine = array_shift($lineGroup->lines);
                if (!empty($groupedResult)) {
                    $previousLine = array_pop($groupedResult);
                    $groupedResult[] = $previousLine . $firstLine;
                } else {
                    $groupedResult[] = $firstLine;
                }
            } else {
                $firstLine = array_shift($lineGroup->lines);
                $otherLines = $lineGroup;
                if (!empty($groupedResult)) {
                    $previousLine = array_pop($groupedResult);
                    $groupedResult[] = $previousLine . $firstLine;
                } else {
                    $groupedResult[] = $firstLine;
                }
                $groupedResult = array_merge($groupedResult, $otherLines->lines);
            }
        }
        $groupedResult = array_map(
            fn (string $line) => str_replace(DocumentPrinter::NonBreakingSpace, ' ', $line),
            $groupedResult,
        );
        return $this->clone(lineGroups: [new PrintResultLineGroup($groupedResult)]);
    }

    public function fitHorizontallyIn(PrintingContext $context): bool
    {
        $firstLine = true;
        foreach ($this->lineGroups as $lineGroup) {
            foreach ($lineGroup->lines as $line) {
                if ($firstLine && !$context->fitsInline($line)) {
                    return false;
                } elseif (!$firstLine && !$context->fitsInNewline($line)) {
                    return false;
                }
                $firstLine = false;
            }
        }
        return true;
    }

    public function indentAllLines(PrintingContext $context, bool $exceptFirst = false): PrintResults
    {
        $clone = $this->clone();
        foreach($clone->lineGroups as $lineGroupIndex => $lineGroup) {
            $lineGroup->indentAllLines($context, $lineGroupIndex === 0 && $exceptFirst);
        }
        return $clone;
    }
}
