<?php

namespace CrazyCodeGen\Formatting\Components\Interactions;

use CrazyCodeGen\Formatting\Components\ContextInteraction;
use CrazyCodeGen\Formatting\PrintingContext;

class Indent implements ContextInteraction
{
    public function supports(PrintingContext $context): bool
    {
        return true;
    }

    public function applyContextChanges(PrintingContext $context): PrintingContext
    {
        $context->indent();
        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalComponents(PrintingContext $context): array
    {
        return [];
    }
}