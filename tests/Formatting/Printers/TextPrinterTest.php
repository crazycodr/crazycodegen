<?php

namespace CrazyCodeGen\Tests\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\Printers\TextPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Tests\Formatting\Traits\SharedDocumentPrinterSetupTrait;
use PHPUnit\Framework\TestCase;

class TextPrinterTest extends TestCase
{
    use SharedDocumentPrinterSetupTrait;

    public function testPrintSimpleTextReturnsTextAsOneSingleGroupAndOneSingleLine()
    {
        $documentPrinter = $this->getDocumentPrinterWithAllPrinters();
        $printer = $documentPrinter->getPrinter(TextPrinter::class);

        $context = new PrintingContext();

        $component = new Text('Hello world');
        $renderedLines = $printer->renderLines($documentPrinter, $context, $component);

        $this->assertCount(1, $renderedLines->lineGroups);
        $this->assertCount(1, $renderedLines->lineGroups[0]->lines);
        $this->assertEquals('Hello world', $renderedLines->lineGroups[0]->lines[0]);
    }
}