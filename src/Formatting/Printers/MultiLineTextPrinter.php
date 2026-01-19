<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\MultilineText;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResultLineGroup;
use CrazyCodeGen\Formatting\PrintResults;

class MultiLineTextPrinter implements ComponentPrinter
{
    public function supportsComponent(PrintableComponent $component): bool
    {
        return $component instanceof MultilineText;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @param PrintableComponent&MultilineText $component
     * @return PrintResults
     */
    public function renderLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent $component,
    ): PrintResults {
        $lines = explode("\n", $component->text);
        return new PrintResults(lineGroups: [new PrintResultLineGroup($lines)]);
    }
}
