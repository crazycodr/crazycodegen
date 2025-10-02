<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Traits\ValidationTrait;

trait HasNameTrait
{
    use ValidationTrait;

    public string $name;

    /**
     * @throws InvalidIdentifierFormatException
     */
    public function setName(string $name): static
    {
        $this->name = $this->assertIsValidIdentifier($name);
        return $this;
    }
}
