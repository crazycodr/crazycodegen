<?php

namespace CrazyCodeGen\Formatting\Formatters\Definitions\Structures;

use CrazyCodeGen\Definition\Definitions\Structures\ConstantDef;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingFormatterFoundException;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\FormattingContext;

class ConstantDefFormatter implements ComponentFormatter
{
    public function getSupportedTargets(): array
    {
        return [ConstantDef::class];
    }

    /**
     * @inheritDoc
     * @param DocumentFormatter $formatter
     * @param mixed&ConstantDef $candidate
     * @return DocumentComponent
     * @throws NoSupportingFormatterFoundException
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent {
        $groupTokens = [];
        $groupTokens[] = new Text($candidate->visibility->value);
        $groupTokens[] = new Text(' ');
        $groupTokens[] = new Text('const');
        $groupTokens[] = new Text(' ');
        $groupTokens[] = $formatter->format($context, $candidate->type);
        $groupTokens[] = new Text(' ');
        $groupTokens[] = new Text($candidate->name);
        $groupTokens[] = new Text(' ');
        $groupTokens[] = new Text('=');
        $groupTokens[] = new Text(' ');
        $groupTokens[] = $formatter->format($context, $candidate->value);
        $groupTokens[] = new Text(';');
        return new HorizontalLayoutGroup($groupTokens);
    }
}
