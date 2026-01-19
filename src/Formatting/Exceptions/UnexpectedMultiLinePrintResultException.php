<?php

namespace CrazyCodeGen\Formatting\Exceptions;

use CrazyCodeGen\Formatting\PrintResults;
use Exception;

class UnexpectedMultiLinePrintResultException extends Exception
{
    public function __construct(
        public readonly PrintResults $printResults,
    ) {
        parent::__construct('Unexpected multiline print result.');
    }
}
