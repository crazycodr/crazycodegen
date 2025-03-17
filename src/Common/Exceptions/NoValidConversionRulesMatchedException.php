<?php

namespace CrazyCodeGen\Common\Exceptions;

use Exception;

class NoValidConversionRulesMatchedException extends Exception
{
    public function __construct() {
        parent::__construct('No valid conversion rule matched when validating and converting a value.');
    }
}
