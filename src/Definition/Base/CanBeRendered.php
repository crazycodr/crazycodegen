<?php

namespace CrazyCodeGen\Definition\Base;

use CrazyCodeGen\Definition\Exceptions\NonComputableValueException;
use CrazyCodeGen\Definition\Renderers\ContextShift;
use CrazyCodeGen\Definition\Tokens\Token;

interface CanBeRendered
{
    /**
     * @return array<string|ContextShift|Token>
     *
     * @throws NonComputableValueException
     */
    public function getTokens(): array;
}
