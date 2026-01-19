<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules;

use CrazyCodeGen\Core\JsonDeserializable;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\ArrayRules;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules\StringRules;
use JsonSerializable;

class ValueRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly StringRules $strings = new StringRules(),
        public readonly ArrayRules $arrays = new ArrayRules(),
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            StringRules::jsonDeserialize($data['strings'] ?? []),
            ArrayRules::jsonDeserialize($data['arrays'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'strings' => $this->strings->jsonSerialize(),
            'arrays' => $this->arrays->jsonSerialize(),
        ];
    }
}
