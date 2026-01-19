<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules;

use CrazyCodeGen\Core\JsonDeserializable;
use JsonSerializable;

class ArrayRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly ArraySyntaxEnum $syntax = ArraySyntaxEnum::Short,
        public readonly ArrayMultilineStrategyEnum $multilineStrategy = ArrayMultilineStrategyEnum::ChopDownIfLong,
        public readonly ArrayTrailingCommaEnum $trailingComma = ArrayTrailingCommaEnum::MultilineOnly,
        public readonly bool $alignKeyValues = false,
        public readonly bool $padInsideBrackets = false,
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            isset($data['syntax']) ? ArraySyntaxEnum::from($data['syntax']) : ArraySyntaxEnum::Short,
            isset($data['multilineStrategy']) ? ArrayMultilineStrategyEnum::from($data['multilineStrategy']) : ArrayMultilineStrategyEnum::ChopDownIfLong,
            isset($data['trailingComma']) ? ArrayTrailingCommaEnum::from($data['trailingComma']) : ArrayTrailingCommaEnum::MultilineOnly,
            $data['alignKeyValues'] ?? false,
            $data['padInsideBrackets'] ?? false,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'syntax' => $this->syntax->value,
            'multilineStrategy' => $this->multilineStrategy->value,
            'trailingComma' => $this->trailingComma->value,
            'alignKeyValues' => $this->alignKeyValues,
            'padInsideBrackets' => $this->padInsideBrackets,
        ];
    }
}
