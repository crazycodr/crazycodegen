<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules;

use CrazyCodeGen\Core\JsonDeserializable;
use JsonSerializable;

class StringRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly StringWrappingTokenEnum $wrappingToken = StringWrappingTokenEnum::SingleQuotes,
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        $wrappingTokenValue = $data['wrappingToken'] ?? null;
        $wrappingToken = $wrappingTokenValue ? StringWrappingTokenEnum::from($wrappingTokenValue) : StringWrappingTokenEnum::SingleQuotes;

        return new self($wrappingToken);
    }

    public function jsonSerialize(): array
    {
        return [
            'wrappingToken' => $this->wrappingToken->value,
        ];
    }
}
