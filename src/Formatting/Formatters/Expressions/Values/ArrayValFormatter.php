<?php

namespace CrazyCodeGen\Formatting\Formatters\Expressions\Values;

use CrazyCodeGen\Definition\Definitions\Values\ArrayVal;
use CrazyCodeGen\Definition\Definitions\Values\StringVal;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\Documents\AdaptiveLayoutGroup;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayGroup;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayKey;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayPair;
use CrazyCodeGen\Formatting\Components\Documents\AssociativeArrayValue;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\Components\Interactions\IfNotVertical;
use CrazyCodeGen\Formatting\Components\Interactions\IfVertical;
use CrazyCodeGen\Formatting\Components\Interactions\Indent;
use CrazyCodeGen\Formatting\Components\Interactions\Outdent;
use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\DocumentPrinter;
use CrazyCodeGen\Formatting\Exceptions\NoSupportingFormatterFoundException;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\FormattingContext;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArrayMultilineStrategyEnum;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArraySyntaxEnum;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArrayTrailingCommaEnum;

/**
 * Used to format array values into token streams that correspond to the shape of an array.
 * Arrays always return as a DropdownGroup because it usually can be broken into multiple lines when not enough space.
 */
class ArrayValFormatter implements ComponentFormatter
{
    public function getSupportedTargets(): array
    {
        return [ArrayVal::class];
    }

    /**
     * @inheritDoc
     * @param DocumentFormatter $formatter
     * @param FormattingContext $context
     * @param mixed&ArrayVal $candidate
     * @return DocumentComponent
     * @throws NoSupportingFormatterFoundException
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent {
        $rules = $formatter->rules->expressions->values->arrays;

        $startToken = $rules->syntax === ArraySyntaxEnum::Short ? '[' : 'array(';
        $endToken = $rules->syntax === ArraySyntaxEnum::Short ? ']' : ')';

        $favorsRecursiveVerticalLayout = $rules->multilineStrategy === ArrayMultilineStrategyEnum::ChopDownIfLong;
        $forcesVerticalLayout = $rules->multilineStrategy === ArrayMultilineStrategyEnum::AlwaysMultiline;

        $rootComponent = new AdaptiveLayoutGroup(
            favorsRecursiveVerticalLayout: $favorsRecursiveVerticalLayout,
            forceVerticalLayout: $forcesVerticalLayout,
        );

        $rootComponent->subComponents[] = new Text($startToken);
        if ($rules->padInsideBrackets) {
            $rootComponent->subComponents[] = new IfNotVertical([new Text(' ')]);
        }
        $rootComponent->subComponents[] = new IfVertical([new Indent()]);
        $rootComponent->subComponents[] = $innerListComponent = new AssociativeArrayGroup([]);
        
        $items = $candidate->keyValues;
        $count = count($items);
        $i = 0;

        foreach ($items as $key => $value) {
            $i++;
            
            $keyComponent = null;
            $arrowComponent = null;
            if (is_string($key)) {
                $keyComponent = new AssociativeArrayKey([
                    $formatter->format($context, new StringVal($key))
                ]);
                $arrowComponent = new Text(DocumentPrinter::NonBreakingSpace . '=> ');
            }
            
            if (is_string($value)) {
                $value = new StringVal($value);
            }
            $valueComponent = new AssociativeArrayValue();
            $valueComponent->subComponents[] = $formatter->format($context, $value);
            
            // Handle comma logic
            $isLast = $i === $count;
            
            if ($rules->trailingComma === ArrayTrailingCommaEnum::Always) {
                $valueComponent->subComponents[] = new Text(',');
                $valueComponent->subComponents[] = new IfNotVertical([new Text(' ')]);
            } elseif ($isLast && $rules->trailingComma === ArrayTrailingCommaEnum::MultilineOnly) {
                $valueComponent->subComponents[] = new IfVertical([new Text(',')]);
            } elseif (!$isLast) {
                $valueComponent->subComponents[] = new Text(',');
                $valueComponent->subComponents[] = new IfNotVertical([new Text(' ')]);
            }
            
            $innerListComponent->pairs[] = new AssociativeArrayPair($keyComponent, $arrowComponent, $valueComponent);
        }

        $rootComponent->subComponents[] = new IfVertical([new Outdent()]);
        if ($rules->padInsideBrackets) {
            $rootComponent->subComponents[] = new IfNotVertical([new Text(' ')]);
        }
        $rootComponent->subComponents[] = new Text($endToken);
        return $rootComponent;
    }
}
