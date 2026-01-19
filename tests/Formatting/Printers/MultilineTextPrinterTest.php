<?php

namespace CrazyCodeGen\Tests\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\MultilineText;
use CrazyCodeGen\Formatting\Printers\MultiLineTextPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Tests\Formatting\Traits\SharedDocumentPrinterSetupTrait;
use PHPUnit\Framework\TestCase;

class MultilineTextPrinterTest extends TestCase
{
    use SharedDocumentPrinterSetupTrait;

    public function testPrintMultilineTextReturnsTextSplitOnNewLines()
    {
        $documentPrinter = $this->getDocumentPrinterWithAllPrinters();
        $printer = $documentPrinter->getPrinter(MultiLineTextPrinter::class);

        $context = new PrintingContext();

        $component = new MultilineText("Hello\nworld");
        $renderedLines = $printer->renderLines($documentPrinter, $context, $component);

        $this->assertCount(1, $renderedLines->lineGroups);
        $this->assertCount(2, $renderedLines->lineGroups[0]->lines);
        $this->assertEquals(['Hello', 'world'], $renderedLines->lineGroups[0]->lines);
    }
}