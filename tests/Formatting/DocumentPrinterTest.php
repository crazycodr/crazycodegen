<?php

namespace CrazyCodeGen\Tests\Formatting;

use CrazyCodeGen\Formatting\Components\Documents\AdaptiveLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\Components\Documents\VerticalLayoutGroup;
use CrazyCodeGen\Formatting\Components\DropdownGroup;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingPrinterFoundException;
use CrazyCodeGen\Formatting\Printers\DropdownGroupPrinter;
use CrazyCodeGen\Formatting\Printers\GroupPrinter;
use CrazyCodeGen\Formatting\Printers\MultiLineTextPrinter;
use CrazyCodeGen\Formatting\Printers\TextPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Tests\Formatting\Traits\SharedDocumentPrinterSetupTrait;
use PHPUnit\Framework\TestCase;
use TypeError;

class DocumentPrinterTest extends TestCase
{
    use SharedDocumentPrinterSetupTrait;

    public function testPrintersAddedThroughConstructorAreAddedToPrinterListByClassName()
    {
        $printer = new DocumentPrinter([new TextPrinter()]);

        $this->assertEquals([
            TextPrinter::class => new TextPrinter(),
        ], $printer->getPrinters());
    }

    public function testPrintersAddedThroughConstructorAreValidatedByUsingAddPrinters()
    {
        $this->expectException(TypeError::class);
        new DocumentPrinter([new DocumentPrinter()]);
    }

    public function testAddPrintersAddsPrintersToTheListByClassName()
    {
        $printer = new DocumentPrinter();
        $printer->addPrinters(new TextPrinter(), new MultiLineTextPrinter());

        $this->assertEquals([
            TextPrinter::class => new TextPrinter(),
            MultiLineTextPrinter::class => new MultiLineTextPrinter(),
        ], $printer->getPrinters());
    }

    public function testAddedPrintersReturnWhenFetchedOrReturnNullWhenNotFound()
    {
        $printer = new DocumentPrinter();
        $printer->addPrinters(new TextPrinter(), new MultiLineTextPrinter());

        $this->assertEquals(new TextPrinter(), $printer->getPrinter(TextPrinter::class));
        $this->assertNull($printer->getPrinter(GroupPrinter::class));
    }

    /**
     * @throws NoSupportingPrinterFoundException
     */
    public function testPrintDocumentPrintsTheMainComponentAndJoinsResultingLinesUsingNewLine()
    {
        $printer = $this->getDocumentPrinterWithAllPrinters();

        $context = new PrintingContext(inlineSpaceAvailable: 10, newLineSpaceAvailable: 60);
        $document = new VerticalLayoutGroup([
            new Text('FooBarBaz'),
            new Text('Helloworld'),
        ]);
        $results = $printer->printDocument($context, $document);

        $this->assertEquals("FooBarBaz\nHelloworld", $results);
    }

    /**
     * @throws NoSupportingPrinterFoundException
     */
    public function testPrintComponentReturnsTheRawPrintResult()
    {
        $printer = $this->getDocumentPrinterWithAllPrinters();

        $context = new PrintingContext(inlineSpaceAvailable: 10, newLineSpaceAvailable: 60);
        $document = new AdaptiveLayoutGroup([
            new Text('FooBarBaz'),
            new Text('Helloworld'),
        ]);
        $results = $printer->printComponent($context, $document);

        $this->assertCount(1, $results->lineGroups);
        $this->assertCount(2, $results->lineGroups[0]->lines);
        $this->assertEquals(["FooBarBaz", "Helloworld"], $results->lineGroups[0]->lines);
    }
}