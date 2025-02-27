<?php

namespace CrazyCodeGen\Base;

use CrazyCodeGen\Exceptions\NonComputableValueException;
use CrazyCodeGen\Renderers\ContextShift;

interface CanBeRendered
{
    /**
     * @return array<string|ContextShift>
     *
     * @throws NonComputableValueException
     */
    public function getTokens(): array;
}
