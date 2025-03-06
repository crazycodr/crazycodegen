<?php

namespace CrazyCodeGen\Definition\Factories;

use CrazyCodeGen\Definition\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;

abstract class TokenConverter
{
    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    abstract public function convertTokens(array $tokens): array;

    public function getTrigger(): string
    {
        return '';
    }
}
