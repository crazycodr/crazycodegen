<?php

namespace CrazyCodeGen\Formatting\Formatters;

use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\FormattingContext;

interface ComponentFormatter
{
    /**
     * Returns AST components supported by this printer.
     * @return string[]
     */
    public function getSupportedTargets(): array;

    /**
     * Converts an AST candidate to a printable token stream.
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent;
}