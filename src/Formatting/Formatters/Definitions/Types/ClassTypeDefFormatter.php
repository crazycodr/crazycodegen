<?php

namespace CrazyCodeGen\Formatting\Formatters\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\FormattingContext;

class ClassTypeDefFormatter implements ComponentFormatter
{
    public function getSupportedTargets(): array
    {
        return [ClassTypeDef::class];
    }

    /**
     * @inheritDoc
     * @param DocumentFormatter $formatter
     * @param mixed&ClassTypeDef $candidate
     * @return DocumentComponent
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent {
        $groupTokens = [];
        if ($context->hasImportDefForType($candidate)) {
            $groupTokens[] = new Text($candidate->shortName);
        } else {
            $groupTokens[] = new Text($candidate->type);
        }
        return new HorizontalLayoutGroup($groupTokens);
    }
}
