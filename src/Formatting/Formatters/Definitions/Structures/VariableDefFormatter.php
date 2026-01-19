<?php

namespace CrazyCodeGen\Formatting\Formatters\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\VariableDef;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\FormattingContext;

class VariableDefFormatter implements ComponentFormatter
{
    public function getSupportedTargets(): array
    {
        return [VariableDef::class];
    }

    /**
     * @inheritDoc
     * @param DocumentFormatter $formatter
     * @param mixed&VariableDef $candidate
     * @return DocumentComponent
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent {
        $groupTokens = [];
        $groupTokens[] = new Text('$');
        $groupTokens[] = new Text($candidate->name);
        return new HorizontalLayoutGroup($groupTokens);
    }
}
