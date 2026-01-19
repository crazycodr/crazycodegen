<?php

namespace CrazyCodeGen\Formatting\Components\Interactions;

use CrazyCodeGen\Formatting\Components\ContextInteraction;
use CrazyCodeGen\Formatting\PrintingContext;

class Outdent implements ContextInteraction
{
    public function supports(PrintingContext $context): bool
    {
        return true;
    }

    public function applyContextChanges(PrintingContext $context): PrintingContext
    {
        $context->outdent();
        return $context;
    }

    public function getAdditionalComponents(PrintingContext $context): array
    {
        return [];
    }
}