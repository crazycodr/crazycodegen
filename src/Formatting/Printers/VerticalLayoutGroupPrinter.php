<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\VerticalLayoutGroup;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;

class VerticalLayoutGroupPrinter implements ComponentPrinter
{
    use SubComponentsRenderingTrait;

    public function supportsComponent(PrintableComponent $component): bool
    {
        return $component instanceof VerticalLayoutGroup;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @param PrintableComponent&VerticalLayoutGroup $component
     * @return PrintResults
     * @throws NoSupportingPrinterFoundException
     */
    public function renderLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent $component,
    ): PrintResults {
        $printResults = $this->renderSubComponentsToLines(
            $documentPrinter,
            $parentContext->clone(),
            ...$component->subComponents,
        );
        $compressedResult = $printResults->compress();
        return $compressedResult->indentAllLines($parentContext, exceptFirst: true);
    }
}
