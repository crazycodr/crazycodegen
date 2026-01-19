<?php

namespace CrazyCodeGen\Tests\Formatting\Printers;

use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayGroup;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayKey;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayPair;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayValue;
use CrazyCodeGen\Formatting\Components\Documents\MultilineText;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\PrintingContext;
use CrazyCodeGen\Tests\Formatting\Traits\SharedDocumentPrinterSetupTrait;
use PHPUnit\Framework\TestCase;

class AssociativeArrayPrinterTest extends TestCase
{
    use SharedDocumentPrinterSetupTrait;

    public function testRendersInlineWhenItFits()
    {
        $printer = $this->getDocumentPrinterWithAllPrinters();
        $context = new PrintingContext(inlineSpaceAvailable: 100);

        $nbsp = DocumentPrinter::NonBreakingSpace;
        $component = new AssociativeArrayGroup([
            new AssociativeArrayPair(
                new AssociativeArrayKey([new Text('"short"')]),
                new Text($nbsp . '=> '),
                new AssociativeArrayValue([
                    new Text('"val1"'),
                    new Text(','),
                    new Text(' '),
                ]),
            ),
            new AssociativeArrayPair(
                new AssociativeArrayKey([new Text('"longer_key"')]),
                new Text($nbsp . '=> '),
                new AssociativeArrayValue([new Text('"val2"')])
            ),
        ]);

        $printedDocument = $printer->printDocument($context, $component);
        $this->assertEquals(
            '"short" => "val1", "longer_key" => "val2"',
            $printedDocument
        );
    }

    public function testRendersAsArrayWhenForced()
    {
        $printer = $this->getDocumentPrinterWithAllPrinters();
        $context = new PrintingContext(
            inlineSpaceAvailable: 100,
            isVertical: true,
        );

        $nbsp = DocumentPrinter::NonBreakingSpace;
        $component = new AssociativeArrayGroup([
            new AssociativeArrayPair(
                new AssociativeArrayKey([new Text('"short"')]),
                new Text($nbsp . '=> '),
                new AssociativeArrayValue([new Text('"val1"')])
            ),
            new AssociativeArrayPair(
                new AssociativeArrayKey([new Text('"longer_key"')]),
                new Text($nbsp . '=> '),
                new AssociativeArrayValue([new Text('"val2"')])
            ),
        ]);

        $printedDocument = $printer->printDocument($context, $component);
        $this->assertEquals(<<<EOT
            "short"      => "val1"
            "longer_key" => "val2"
            EOT,
            $printedDocument,
        );
    }

    public function testAlignsMultilineValuesTopRelativeToBaselineWithoutPadding()
    {
        $printer = $this->getDocumentPrinterWithAllPrinters();
        $context = new PrintingContext(inlineSpaceAvailable: 100);

        $nbsp = DocumentPrinter::NonBreakingSpace;
        $component = new AssociativeArrayGroup([
            new AssociativeArrayPair(
                new AssociativeArrayKey([new Text('"key"')]),
                new Text($nbsp . '=> '),
                new AssociativeArrayValue([new MultilineText("'multi\nline\nvalue'")]),
            ),
        ]);

        $expected = <<<EOT
            "key" => 'multi
            line
            value'
            EOT;

        $this->assertEquals($expected, $printer->printDocument($context, $component));
    }
}
