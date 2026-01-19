<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\AdaptiveLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\VerticalLayoutGroup;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Enums\VerticalLayoutStrategyEnum;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;

class AdaptiveLayoutGroupPrinter implements ComponentPrinter
{
    use SubComponentsRenderingTrait;

    public function supportsComponent(PrintableComponent $component): bool
    {
        return $component instanceof AdaptiveLayoutGroup;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @param PrintableComponent&AdaptiveLayoutGroup $component
     * @return PrintResults
     * @throws NoSupportingPrinterFoundException
     */
    public function renderLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent $component,
    ): PrintResults {
        if ($component->forceVerticalLayout) {
            $localContext = $parentContext->clone(dropDownStrategy: VerticalLayoutStrategyEnum::Force);
        } elseif ($component->favorsRecursiveVerticalLayout) {
            $localContext = $parentContext->clone(dropDownStrategy: VerticalLayoutStrategyEnum::Favors);
        } else {
            $localContext = $parentContext->clone();
        }
        $horizontalContext = $localContext->clone(indentationLevel: 0, isVertical: false);
        $verticalContext = $localContext->clone(indentationLevel: 0, isVertical: true);
        if ($localContext->verticalLayoutStrategy !== VerticalLayoutStrategyEnum::Force) {
            $printResults = $this->renderSubComponentsToLines(
                $documentPrinter,
                $horizontalContext,
                new HorizontalLayoutGroup($component->subComponents),
            );
            if (!$printResults->fitsHorizontally && $parentContext->verticalLayoutStrategy === VerticalLayoutStrategyEnum::Favors) {
                // Parent said it favors vertical if not fitting, return it upwards for proper recursively forced strategy
                return $printResults;
            } elseif (!$printResults->fitsHorizontally && $horizontalContext->verticalLayoutStrategy === VerticalLayoutStrategyEnum::Favors) {
                // Parent did not say it favors vertical if not fitting, that was us, force the result to rerender vertically
                $parentContext->verticalLayoutStrategy = VerticalLayoutStrategyEnum::Force;
                $horizontalContext->verticalLayoutStrategy = VerticalLayoutStrategyEnum::Force;
                $verticalContext->verticalLayoutStrategy = VerticalLayoutStrategyEnum::Force;
            } elseif($parentContext->verticalLayoutStrategy === VerticalLayoutStrategyEnum::Refuse) {
                // Parent/Local context says we refuse to go vertical, return the results
                return $printResults;
            } elseif ($printResults->fitsHorizontally) {
                // Results fit, return them
                return $printResults;
            }
        }

        $printResults = $this->renderSubComponentsToLines(
            $documentPrinter,
            $verticalContext,
            new VerticalLayoutGroup($component->subComponents),
        );
        return $printResults->compress();
    }
}
