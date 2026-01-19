<?php

namespace CrazyCodeGen\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayGroup;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayPair;
use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\Exceptions\UnexpectedMultiLinePrintResultException;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Formatting\PrintResults;
use Exception;

class AssociativeArrayGroupPrinter implements ComponentPrinter
{
    use SubComponentsRenderingTrait;

    public function supportsComponent(PrintableComponent $component): bool
    {
        return $component instanceof AssociativeArrayGroup;
    }

    /**
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @param PrintableComponent&AssociativeArrayGroup $component
     * @return PrintResults
     * @throws NoSupportingPrinterFoundException
     * @throws Exception
     */
    public function renderLines(
        DocumentPrinter    $documentPrinter,
        PrintingContext    $parentContext,
        PrintableComponent $component,
    ): PrintResults {
        if ($parentContext->isVertical === false) {
            return $this->renderHorizontal($component, $documentPrinter, $parentContext);
        } else {
            $anyHaveKeysAndArrows = array_reduce(
                $component->pairs,
                fn (bool $accumulator, AssociativeArrayPair $pair) => $accumulator && $pair->hasKeyAndArrow(),
                true
            );
            if ($anyHaveKeysAndArrows) {
                $minKeyWidth = 0;
                foreach ($component->pairs as $pair) {
                    if (!$pair->hasKeyAndArrow()) {
                        continue;
                    }
                    $printResults = $this->renderSubComponentsToLines(
                        $documentPrinter,
                        $parentContext->clone(isVertical: false),
                        new HorizontalLayoutGroup($pair->key->subComponents),
                    );
                    if (count($printResults->lineGroups) > 1 || count($printResults->lineGroups[0]->lines) > 1) {
                        throw new UnexpectedMultiLinePrintResultException($printResults);
                    }
                    $minKeyWidth = max($minKeyWidth, mb_strlen($printResults->lineGroups[0]->lines[0]));
                }
                foreach ($component->pairs as $pair) {
                    if (!$pair->hasKeyAndArrow()) {
                        continue;
                    }
                    $printResults = $this->renderSubComponentsToLines(
                        $documentPrinter,
                        $parentContext->clone(isVertical: false),
                        new HorizontalLayoutGroup($pair->key->subComponents),
                    );
                    if (count($printResults->lineGroups) > 1 || count($printResults->lineGroups[0]->lines) > 1) {
                        throw new UnexpectedMultiLinePrintResultException($printResults);
                    }
                    $keyLength = mb_strlen($printResults->lineGroups[0]->lines[0]);
                    if ($keyLength < $minKeyWidth) {
                        $pair->key->subComponents[] = new Text(str_repeat(' ', $minKeyWidth - $keyLength));
                    }
                }
            }
            $finalLines = new PrintResults();
            foreach ($component->pairs as $pair) {
                $printResults = $this->renderSubComponentsToLines(
                    $documentPrinter,
                    $parentContext->clone(isVertical: true),
                    new HorizontalLayoutGroup([
                        ...$pair->key->subComponents,
                        $pair->arrow,
                        ...$pair->value->subComponents,
                    ]),
                );
                $finalLines->addLineGroups(...$printResults->lineGroups);
            }
            $compressedLines = $finalLines->compress();
            return $compressedLines->indentAllLines($parentContext);
        }
    }

    /**
     * @param AssociativeArrayGroup $component
     * @param DocumentPrinter $documentPrinter
     * @param PrintingContext $parentContext
     * @return PrintResults
     * @throws NoSupportingPrinterFoundException
     */
    public function renderHorizontal(
        AssociativeArrayGroup $component,
        DocumentPrinter $documentPrinter,
        PrintingContext $parentContext
    ): PrintResults {
        $finalLines = new PrintResults();
        foreach ($component->pairs as $pair) {
            if ($pair->key && $pair->arrow) {
                $subComponents = [
                    ...$pair->key->subComponents,
                    $pair->arrow,
                    ...$pair->value->subComponents,
                ];
            } else {
                $subComponents = $pair->value->subComponents;
            }
            $printResults = $this->renderSubComponentsToLines(
                $documentPrinter,
                $parentContext->clone(),
                new HorizontalLayoutGroup($subComponents),
            );
            if (!$printResults->fitsHorizontally) {
                return $printResults;
            }
            $finalLines->addLineGroups(...$printResults->lineGroups);
        }
        $finalLines = $finalLines->inlineMerge();
        $finalLines->fitsHorizontally = $finalLines->fitHorizontallyIn($parentContext);
        return $finalLines;
    }
}
