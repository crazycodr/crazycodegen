<?php

namespace CrazyCodeGen\Formatting\Components\Interactions;

use CrazyCodeGen\Formatting\Components\ContextInteraction;
use CrazyCodeGen\Formatting\Components\PrintableComponent;
use CrazyCodeGen\Formatting\PrintingContext;

class IfNotVertical implements ContextInteraction
{
    public function __construct(
        /** @var PrintableComponent[] $subComponents */
        public array $subComponents = [],
    ) {
    }

    public function supports(PrintingContext $context): bool
    {
        return $context->isVertical === false;
    }

    public function applyContextChanges(PrintingContext $context): PrintingContext
    {
        foreach ($this->subComponents as $subComponent) {
            if ($subComponent instanceof ContextInteraction && $subComponent->supports($context)) {
                $context = $subComponent->applyContextChanges($context);
            }
        }
        return $context;
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalComponents(PrintingContext $context): array
    {
        if ($this->supports($context)) {
            return $this->subComponents;
        }
        return [];
    }
}