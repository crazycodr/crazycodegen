<?php

namespace CrazyCodeGen\Formatting\Exceptions;

use Exception;
use Throwable;

class NoSupportingPrinterFoundException extends Exception
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct(message: 'No support printer found for component', previous: $previous);
    }
}