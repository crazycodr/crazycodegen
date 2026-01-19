<?php

namespace CrazyCodeGen\Tests\Formatting\Traits;

use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Printers\AdaptiveLayoutGroupPrinter;
use CrazyCodeGen\Formatting\Printers\AssociativeArrayGroupPrinter;
use CrazyCodeGen\Formatting\Printers\DropdownGroupPrinter;
use CrazyCodeGen\Formatting\Printers\DropdownTableGroupPrinter;
use CrazyCodeGen\Formatting\Printers\GroupPrinter;
use CrazyCodeGen\Formatting\Printers\HorizontalLayoutGroupPrinter;
use CrazyCodeGen\Formatting\Printers\MultiLineTextPrinter;
use CrazyCodeGen\Formatting\Printers\TextPrinter;
use CrazyCodeGen\Formatting\Printers\VerticalLayoutGroupPrinter;

trait SharedDocumentPrinterSetupTrait
{
    private function getDocumentPrinterWithAllPrinters(): DocumentPrinter
    {
        return new DocumentPrinter([
            new TextPrinter(),
            new MultiLineTextPrinter(),
            new AssociativeArrayGroupPrinter(),
            new HorizontalLayoutGroupPrinter(),
            new VerticalLayoutGroupPrinter(),
            new AdaptiveLayoutGroupPrinter(),
        ]);
    }
}
