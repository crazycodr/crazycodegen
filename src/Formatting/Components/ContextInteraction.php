<?php

namespace CrazyCodeGen\Formatting\Components;

use CrazyCodeGen\Formatting\PrintingContext;

interface ContextInteraction extends PrintableComponent
{
    public function supports(PrintingContext $context): bool;
    public function applyContextChanges(PrintingContext $context): PrintingContext;
    /**
     * @param PrintingContext $context
     * @return PrintableComponent[]
     */
    public function getAdditionalComponents(PrintingContext $context): array;
}