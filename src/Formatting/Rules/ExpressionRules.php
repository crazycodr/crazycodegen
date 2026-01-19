<?php

namespace CrazyCodeGen\Formatting\Rules;

use CrazyCodeGen\Core\JsonDeserializable;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\StructureRules;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\ValueRules;
use JsonSerializable;

class ExpressionRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly StructureRules $structures = new StructureRules(),
        public readonly ValueRules $values = new ValueRules(),
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            StructureRules::jsonDeserialize($data['structures'] ?? []),
            ValueRules::jsonDeserialize($data['values'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'structures' => $this->structures->jsonSerialize(),
            'values' => $this->values->jsonSerialize(),
        ];
    }
}
