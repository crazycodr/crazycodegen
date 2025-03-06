<?php

namespace CrazyCodeGen\Definition\Factories;

use CrazyCodeGen\Definition\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;

class TokenConverterGroup extends TokenConverter
{
    public function __construct(
        /** @var TokenConverter[] */
        public array $converters,
        public bool  $useRightAssociativity = false,
    ) {
    }

    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public function convertTokens(array $tokens): array
    {
        if ($this->useRightAssociativity) {
            return $this->convertTokensFromRight($tokens);
        } else {
            return $this->convertTokensFromLeft($tokens);
        }
    }

    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public function convertTokensFromRight(array $tokens): array
    {
        do {
            $newComponentGenerated = false;
            for ($index = count($tokens) - 1; $index >= 0; $index--) {
                $token = $tokens[$index];
                foreach ($this->converters as $converter) {
                    if ($token === $converter->getTrigger()) {
                        $tokens = $converter->convertTokens($tokens);
                        $newComponentGenerated = true;
                        continue 3;
                    }
                }
            }
        } while ($newComponentGenerated);

        return $tokens;
    }

    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public function convertTokensFromLeft(array $tokens): array
    {
        do {
            $newComponentGenerated = false;
            for ($index = 0; $index < count($tokens); $index++) {
                $token = $tokens[$index];
                foreach ($this->converters as $converter) {
                    if ($token === $converter->getTrigger()) {
                        $tokens = $converter->convertTokens($tokens);
                        $newComponentGenerated = true;
                        continue 3;
                    }
                }
            }
        } while ($newComponentGenerated);

        return $tokens;
    }
}
