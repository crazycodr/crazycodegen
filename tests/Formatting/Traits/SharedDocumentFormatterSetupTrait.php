<?php

namespace CrazyCodeGen\Tests\Formatting\Traits;

use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Formatters\Definitions\Structures\ConstantDefFormatter;
use CrazyCodeGen\Formatting\Formatters\Definitions\Structures\VariableDefFormatter;
use CrazyCodeGen\Formatting\Formatters\Definitions\Types\BuiltInTypeSpecFormatter;
use CrazyCodeGen\Formatting\Formatters\Definitions\Types\ClassTypeDefFormatter;
use CrazyCodeGen\Formatting\Formatters\Expressions\Values\ArrayValFormatter;
use CrazyCodeGen\Formatting\Formatters\Expressions\Values\StringValFormatter;
use CrazyCodeGen\Formatting\Rules\Rules;

trait SharedDocumentFormatterSetupTrait
{
    private function getDocumentFormatterWithAllFormatters(Rules $rules): DocumentFormatter
    {
        return new DocumentFormatter($rules, [
            new ConstantDefFormatter(),
            new VariableDefFormatter(),
            new ClassTypeDefFormatter(),
            new ArrayValFormatter(),
            new StringValFormatter(),
            new BuiltInTypeSpecFormatter(),
        ]);
    }
}
