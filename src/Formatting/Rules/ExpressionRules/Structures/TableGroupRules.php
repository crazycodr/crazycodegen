<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules\Structures;

use CrazyCodeGen\Core\JsonDeserializable;
use JsonSerializable;

class TableGroupRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public bool $alignKeyValuePairs = true,
        public int $minSpacesBetweenColumns = 1,
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            alignKeyValuePairs: (bool)($data['alignKeyValuePairs'] ?? true),
            minSpacesBetweenColumns: (int)($data['minSpacesBetweenColumns'] ?? 1),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'alignKeyValuePairs' => $this->alignKeyValuePairs,
        ];
    }
}
