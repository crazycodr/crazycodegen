<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;

interface ComponentPrinter
{
    public function supportsComponent(PrintableComponent $component): bool;

    public function renderLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent $component
    ): PrintResults;
}
