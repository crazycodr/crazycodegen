<?php

namespace CrazyCodeGen\Formatting\Rules;

use CrazyCodeGen\Core\JsonDeserializable;
use JsonSerializable;

class Rules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly ExpressionRules $expressions = new ExpressionRules(),
        public readonly IndentationRules $indentation = new IndentationRules(),
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            ExpressionRules::jsonDeserialize($data['expressions'] ?? []),
            IndentationRules::jsonDeserialize($data['indentation'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'expressions' => $this->expressions->jsonSerialize(),
            'indentation' => $this->indentation->jsonSerialize(),
        ];
    }
}
