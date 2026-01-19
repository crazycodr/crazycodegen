<?php

namespace CrazyCodeGen\Formatting\Rules\ExpressionRules;

use CrazyCodeGen\Core\JsonDeserializable;
use CrazyCodeGen\Formatting\Rules\ExpressionRules\Structures\TableGroupRules;
use JsonSerializable;

class StructureRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly TableGroupRules $tableGroups = new TableGroupRules(),
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            TableGroupRules::jsonDeserialize($data['tableGroups'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'tableGroups' => $this->tableGroups->jsonSerialize(),
        ];
    }
}
