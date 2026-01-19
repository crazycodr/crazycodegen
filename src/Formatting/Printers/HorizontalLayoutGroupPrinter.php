<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;

class HorizontalLayoutGroupPrinter implements ComponentPrinter
{
    use SubComponentsRenderingTrait;

    public function supportsComponent(PrintableComponent $component): bool
    {
        return $component instanceof HorizontalLayoutGroup;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @param PrintableComponent&HorizontalLayoutGroup $component
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
        $printResults = $printResults->inlineMerge();
        $printResults->fitsHorizontally = $printResults->fitHorizontallyIn($parentContext);
        if (!$printResults->fitsHorizontally) {
            return $printResults;
        }
        $printResults = $printResults->inlineMerge();
        $printResults->indentAllLines($parentContext, exceptFirst: true);
        return $this->assertFittingInAvailableSpace($printResults, $parentContext);
    }

    /**
     * @param PrintResults $printResults
     * @param PrintingContext $parentContext
     * @return PrintResults
     */
    private function assertFittingInAvailableSpace(
        PrintResults $printResults,
        PrintingContext $parentContext,
    ): PrintResults {
        foreach ($printResults->lineGroups as $lineGroupIndex => $lineGroup) {
            foreach ($lineGroup->lines as $lineIndex => $line) {
                if ($lineGroupIndex === 0 && $lineIndex === 0 && !$parentContext->fitsInline($lineGroup->lines[$lineIndex])) {
                    return $printResults->clone(fitsHorizontally: false);
                } elseif (
                    !($lineGroupIndex === 0 && $lineIndex === 0)
                    && !$parentContext->fitsInNewline($lineGroup->lines[$lineIndex])
                ) {
                    return $printResults->clone(fitsHorizontally: false);
                }
            }
        }
        return $printResults;
    }
}
