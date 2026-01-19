<?php

namespace CrazyCodeGen\Core;

/**
 * Defines a contract for objects that can be deserialized from a JSON array.
 */
interface JsonDeserializable
{
    /**
     * Creates an instance of the object from a JSON-decoded array.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function jsonDeserialize(array $data): self;
}
