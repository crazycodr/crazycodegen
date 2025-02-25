<?php

namespace CrazyCodeGen\Base;

use CrazyCodeGen\Exceptions\NonComputableValueException;

interface CanBeRendered
{
    /**
     * @return CanBeRendered[]|array
     *
     * @throws NonComputableValueException
     */
    public function getTokens(): array;
}
