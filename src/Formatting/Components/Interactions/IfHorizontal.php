<?php

namespace CrazyCodeGen\Formatting\Components\Interactions;

use CrazyCodeGen\Formatting\Components\ContextInteraction;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\PrintingContext;

class IfHorizontal implements ContextInteraction
{
    public function __construct(
        /** @var PrintableComponent[] $subComponents */
        public array $subComponents = [],
    ) {
    }

    public function supports(PrintingContext $context): bool
    {
        return !$context->isVertical;
    }

    public function applyContextChanges(PrintingContext $context): PrintingContext
    {
        return $context;
    }

    public function getAdditionalComponents(PrintingContext $context): array
    {
        return $this->subComponents;
    }
}
