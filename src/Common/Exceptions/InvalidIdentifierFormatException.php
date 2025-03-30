<?php

namespace CrazyCodeGen\Common\Exceptions;

use Exception;

class InvalidIdentifierFormatException extends Exception
{
    public function __construct(public readonly string $name)
    {
        parent::__construct("Identifier '$name' is invalid");
    }
}
