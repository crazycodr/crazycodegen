<?php

namespace CrazyCodeGen\Formatting;

use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\Printers\ComponentPrinter;
use Exception;

class DocumentPrinter
{
    /**
     * Use this to prevent trimming of merged in elements in Groups.
     * These are replaced with a space after being merged in and allows preserving needed spaces.
     */
    public const NonBreakingSpace = "\u{00A0}";

    /** @var ComponentPrinter[] $printers */
    private array $printers = [];

    public function __construct(
        /** @var ComponentPrinter[] $printers */
        array $printers = [],
    ) {
        $this->addPrinters(...$printers);
    }

    /**
     * @return array<string, ComponentPrinter>
     */
    public function getPrinters(): array
    {
        return $this->printers;
    }

    public function getPrinter(string $class): null|ComponentPrinter
    {
        return $this->printers[$class] ?? null;
    }

    public function addPrinters(ComponentPrinter ...$printers): self
    {
        foreach ($printers as $printer) {
            $this->printers[get_class($printer)] = $printer;
        }
        return $this;
    }

    /**
     * @throws NoSupportingPrinterFoundException
     */
    public function printDocument(PrintingContext $context, DocumentComponent $rootComponent): string
    {
        $printResult = $this->printComponent($context, $rootComponent);
        if (count($printResult->lineGroups) > 1) {
            throw new Exception('Expected only one final line group');
        }
        return join("\n", $printResult->lineGroups[0]->lines);
    }

    /**
     * @param PrintingContext $context
     * @param DocumentComponent $component
     * @return PrintResults
     * @throws NoSupportingPrinterFoundException
     */
    public function printComponent(PrintingContext $context, DocumentComponent $component): PrintResults
    {
        foreach ($this->printers as $printer) {
            if ($printer->supportsComponent($component)) {
                return $printer->renderLines($this, $context, $component);
            }
        }
        throw new NoSupportingPrinterFoundException();
    }
}
