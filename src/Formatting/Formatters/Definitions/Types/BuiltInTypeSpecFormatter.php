<?php

namespace CrazyCodeGen\Formatting\Formatters\Definitions\Types;

use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypesEnum;
use CrazyCodeGen\Definition\Definitions\Types\BuiltInTypeSpec;
use CrazyCodeGen\Formatting\Components\DocumentComponent;
use CrazyCodeGen\Formatting\Components\Documents\Text;
use CrazyCodeGen\Formatting\DocumentFormatter;
use CrazyCodeGen\Formatting\Formatters\ComponentFormatter;
use CrazyCodeGen\Formatting\FormattingContext;

class BuiltInTypeSpecFormatter implements ComponentFormatter
{
    public function getSupportedTargets(): array
    {
        return [BuiltInTypeSpec::class];
    }

    /**
     * @inheritDoc
     * @param DocumentFormatter $formatter
     * @param mixed&BuiltInTypeSpec $candidate
     * @return DocumentComponent
     */
    public function format(
        DocumentFormatter $formatter,
        FormattingContext $context,
        mixed $candidate,
    ): DocumentComponent {
        return match ($candidate->type) {
            // Scalar types
            BuiltInTypesEnum::int => new Text('int'),
            BuiltInTypesEnum::float => new Text('float'),
            BuiltInTypesEnum::bool => new Text('bool'),
            BuiltInTypesEnum::string => new Text('string'),

            // Structural types
            BuiltInTypesEnum::array => new Text('array'),
            BuiltInTypesEnum::object => new Text('object'),
            BuiltInTypesEnum::callable => new Text('callable'),

            // Special types
            BuiltInTypesEnum::void => new Text('void'),

            // Singleton types
            BuiltInTypesEnum::true => new Text('true'),
            BuiltInTypesEnum::false => new Text('false'),

            // Unit types
            BuiltInTypesEnum::null => new Text('null'),

            // Union types
            BuiltInTypesEnum::mixed => new Text('mixed'),
            BuiltInTypesEnum::iterable => new Text('iterable'),
        };
    }
}
