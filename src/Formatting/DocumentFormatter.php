<?php

namespace CrazyCodeGen\Formatting;

use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingFormatterFoundException;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\Rules\Rules;
use Exception;

class DocumentFormatter
{
    /** @var ComponentFormatter[] $formatters */
    private array $formatters = [];
    /** @var array<string, ComponentFormatter> $formatterMap */
    private array $formatterMap = [];

    public function __construct(
        public readonly Rules $rules,
        /** @var ComponentFormatter[] $formatters */
        array                 $formatters = [],
    ) {
        foreach($formatters as $formatter) {
            $this->addFormatter($formatter);
        }
    }

    public function addFormatter(ComponentFormatter $formatter): self
    {
        foreach ($formatter->getSupportedTargets() as $target) {
            $this->formatterMap[$target] = $formatter;
        }
        $this->formatters[] = $formatter;
        return $this;
    }

    /**
     * @return ComponentFormatter[]
     */
    public function getFormatters(): array
    {
        return $this->formatters;
    }

    /**
     * @throws NoSupportingFormatterFoundException
     */
    public function format(FormattingContext $context, mixed $candidate): DocumentComponent
    {
        $class = get_class($candidate);
        if (!isset($this->formatterMap[$class])) {
            throw new NoSupportingFormatterFoundException();
        }

        return $this->formatterMap[$class]->format($this, $context, $candidate);
    }
}
