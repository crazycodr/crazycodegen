<?php

namespace CrazyCodeGen\Formatting\Exceptions;

use Exception;
use Throwable;

class NoSupportingFormatterFoundException extends Exception
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(message: 'No support formatter found for component', previous: $previous);
    }
}