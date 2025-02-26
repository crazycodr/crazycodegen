<?php

namespace CrazyCodeGen\Factories;

use CrazyCodeGen\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;

abstract class TokenConverter
{
    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public abstract function convertTokens(array $tokens): array;

    public function getTrigger(): string
    {
        return '';
    }
}