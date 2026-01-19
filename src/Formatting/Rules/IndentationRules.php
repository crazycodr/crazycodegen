<?php

namespace CrazyCodeGen\Formatting\Rules;

use CrazyCodeGen\Core\JsonDeserializable;
use JsonSerializable;

class IndentationRules implements JsonSerializable, JsonDeserializable
{
    public function __construct(
        public readonly int $size = 4,
        public readonly string $char = ' ',
    ) {
    }

    public static function jsonDeserialize(array $data): self
    {
        return new self(
            $data['size'] ?? 4,
            $data['char'] ?? ' ',
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'size' => $this->size,
            'char' => $this->char,
        ];
    }
}
