<?php

namespace CrazyCodeGen\Definition\Factories;

use CrazyCodeGen\Definition\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;

class PairedTokenConverter extends TokenConverter
{
    public function __construct(
        public string $startTrigger,
        public string $endTrigger,
        public mixed  $extractor,
        public mixed  $subConverter,
    )
    {
    }

    public function getTrigger(): string
    {
        return $this->startTrigger;
    }

    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public function convertTokens(array $tokens): array
    {
        $nestingLevels = 0;
        $startIndex = null;
        $endIndex = null;
        for ($index = 0; $index < count($tokens); $index++) {
            $token = $tokens[$index];
            if ($token === $this->startTrigger) {
                $nestingLevels++;
                if ($nestingLevels === 1) {
                    $startIndex = $index;
                }
            } elseif ($token === $this->endTrigger) {
                $nestingLevels--;
                if ($nestingLevels === 0) {
                    $endIndex = $index;
                    break;
                }
            }
        }
        if ($startIndex !== null && $endIndex !== null) {

            $subTokens = array_slice($tokens, $startIndex + 1, $endIndex - $startIndex - 1);
            $subConverter = $this->subConverter;
            $subConversionResult = $subConverter($subTokens);
            if (!is_array($subConversionResult)) {
                $subConversionResult = [$subConversionResult];
            }

            $extractor = $this->extractor;
            $extractedResult = $extractor($subConversionResult);
            array_splice(
                $tokens,
                $startIndex,
                $endIndex - $startIndex + 1,
                is_array($extractedResult) ? $extractedResult : [$extractedResult]
            );

        } elseif ($startIndex !== null || $endIndex !== null) {
            throw new ExpressionBuildingMissingOperandForFoundTokenException();
        }

        return $tokens;
    }
}