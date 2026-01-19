<?php

namespace CrazyCodeGen\Formatting\Formatters\Expressions\Values;

use CrazyCodeGen\Definition\Definitions\Values\StringVal;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\Documents\HorizontalLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\FormattingContext;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\StringRules;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\StringWrappingTokenEnum;

/**
 * Used to format simple string values stored in StringVal. Does not support
 * interpolation. It renders the StringVal as a single-line string, escaping
 * control characters (like newlines and tabs) and quotes.
 */
class StringValFormatter implements ComponentFormatter
{
    public function getSupportedTargets(): array
    {
        return [StringVal::class];
    }

    /**
     * @inheritDoc
     * @param DocumentFormatter $formatter
     * @param FormattingContext $context
     * @param mixed&StringVal $candidate
     * @return DocumentComponent
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent {
        $stringRules = $formatter->rules->expressions->values->strings;
        $quote = $stringRules->wrappingToken->value;

        $groupTokens = [];
        $groupTokens[] = new Text($quote);
        $escapedString = $this->escapeString($stringRules, $candidate);
        $groupTokens[] = new Text($escapedString);
        $groupTokens[] = new Text($quote);

        return new HorizontalLayoutGroup($groupTokens);
    }

    /**
     * @param StringRules $stringRules
     * @param mixed&StringVal $candidate
     * @return string
     */
    private function escapeString(StringRules $stringRules, mixed $candidate): string
    {
        $search = ['\\', "\r", "\n", "\t"];
        $replace = ['\\\\', '\\r', '\\n', '\\t'];
        if ($stringRules->wrappingToken === StringWrappingTokenEnum::SingleQuotes) {
            // For single quotes, also escape the single quote character
            $search[] = "'";
            $replace[] = "\\'";
        } else {
            // For double quotes, also escape double quotes and the dollar sign
            $search[] = '"';
            $replace[] = '\\"';
            $search[] = '$';
            $replace[] = '\\$';
        }

        return str_replace($search, $replace, $candidate->value);
    }
}
