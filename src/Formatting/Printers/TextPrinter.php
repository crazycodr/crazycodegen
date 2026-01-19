<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;

class TextPrinter implements ComponentPrinter
{
    public function supportsComponent(PrintableComponent $component): bool
    {
        return $component instanceof Text;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @param PrintableComponent&Text $component
     * @return PrintResults
     */
    public function renderLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent $component,
    ): PrintResults {
        return new PrintResults(lineGroups: [$component->text]);
    }
}
