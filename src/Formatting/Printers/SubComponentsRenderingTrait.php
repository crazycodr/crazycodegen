<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\ContextInteraction;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;

trait SubComponentsRenderingTrait
{
    /**
     * @throws NoSupportingPrinterFoundException
     */
    public function renderSubComponentsInlineAndMerge(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent ...$components
    ): PrintResults {
        $localContext = $parentContext->clone(isVertical: false);
        $printResults = $this->renderSubComponentsToLines(
            $documentPrinter,
            $localContext,
            ...$components,
        );
        return $this->mergeLines($printResults);
    }

    /**
     * @throws NoSupportingPrinterFoundException
     */
    public function renderSubComponentsDroppedDownAndFlatten(
        DocumentPrinter $documentPrinter,
        PrintingContext $context,
        PrintableComponent ...$components
    ): PrintResults {
        $printResults = $this->renderSubComponentsToLines(
            $documentPrinter,
            $context->clone(
                inlineSpaceAvailable: $context->newLineSpaceAvailable,
                isVertical: true,
            ),
            ...$components,
        );
        $flattened = new PrintResults(fitsHorizontally: false);
        foreach ($printResults->lineGroups as $lineGroup) {
            foreach ($lineGroup->lines as &$line) {
                $line = $context->getIndentation() . $line;
            }
            $flattened->addLines(...$lineGroup->lines);
        }
        return $flattened;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $localContext
     * @param PrintableComponent ...$components
     * @return PrintResults
     * @throws NoSupportingPrinterFoundException
     */
    public function renderSubComponentsToLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $localContext,
        PrintableComponent ...$components
    ): PrintResults {
        $renderedSubcomponents = new PrintResults();
        foreach ($components as $subComponent) {
            if ($subComponent instanceof ContextInteraction) {
                $localContext = $this->applyContextChanges($localContext, $subComponent);
                $additionalComponents = $this->getAdditionalComponents($localContext, $subComponent);
                if (!empty($additionalComponents)) {
                    $printResults = $this->renderSubComponentsToLines(
                        $documentPrinter,
                        $localContext,
                        ...$additionalComponents
                    );
                    $renderedSubcomponents->fitsHorizontally = $renderedSubcomponents->fitsHorizontally && $printResults->fitsHorizontally;
                    $renderedSubcomponents->addLineGroups(...$printResults->lineGroups);
                }
            }
            if ($subComponent instanceof DocumentComponent) {
                $printResults = $documentPrinter->printComponent($localContext, $subComponent);
                if (!$printResults->fitsHorizontally) {
                    return $printResults;
                }
                $renderedSubcomponents->addLineGroups(...$printResults->lineGroups);
            }
        }
        return $renderedSubcomponents;
    }

    /**
     * @param PrintResults $printResults
     * @return PrintResults
     */
    private function mergeLines(PrintResults $printResults): PrintResults
    {
        $groupedResult = [];
        foreach ($printResults->lineGroups as $lineGroup) {
            if (count($lineGroup->lines) === 1) {
                $firstLine = array_shift($lineGroup->lines);
                if (!empty($groupedResult)) {
                    $previousLine = array_pop($groupedResult);
                    $groupedResult[] = $previousLine . ltrim($firstLine);
                } else {
                    $groupedResult[] = $firstLine;
                }
            } else {
                $firstLine = array_shift($lineGroup->lines);
                $otherLines = $lineGroup;
                if (!empty($groupedResult)) {
                    $previousLine = array_pop($groupedResult);
                    $groupedResult[] = $previousLine . ltrim($firstLine);
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
        return new PrintResults($printResults->fitsHorizontally, $groupedResult);
    }

    private function applyContextChanges(PrintingContext $context, ContextInteraction $interaction): PrintingContext
    {
        if ($interaction->supports($context)) {
            $context = $interaction->applyContextChanges($context);
        }
        return $context;
    }

    /**
     * @param PrintingContext $context
     * @param ContextInteraction $interaction
     * @return PrintableComponent[]
     */
    private function getAdditionalComponents(PrintingContext $context, ContextInteraction $interaction): array
    {
        if ($interaction->supports($context)) {
            return $interaction->getAdditionalComponents($context);
        }
        return [];
    }
}
