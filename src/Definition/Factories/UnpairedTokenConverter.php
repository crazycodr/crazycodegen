<?php

namespace CrazyCodeGen\Definition\Factories;

use CrazyCodeGen\Definition\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;

class UnpairedTokenConverter extends TokenConverter
{
    public function __construct(
        public string      $startTrigger,
        public array       $collectedTokenOffsets = [+1],
        public mixed       $extractor,
        public null|string $lookAheadToken = null,
        public bool        $useRightAssociativity = false,
    ) {
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
        if ($this->useRightAssociativity) {
            return $this->convertTokensFromRight($tokens);
        } else {
            return $this->convertTokensFromLeft($tokens);
        }
    }

    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    private function convertTokensFromRight(array $tokens): array
    {
        for ($index = count($tokens) - 1; $index >= 0; $index--) {
            $token = $tokens[$index];
            if ($token === $this->startTrigger) {
                $lookAheadOffset = 0;
                $lookAheadToken = null;
                if ($this->lookAheadToken) {
                    $lookAheadToken = $tokens[$index + 1];
                    if ($lookAheadToken === $this->lookAheadToken) {
                        $lookAheadOffset = 1;
                    } else {
                        $lookAheadToken = null;
                    }
                }
                $additionalTokens = [];
                foreach ($this->collectedTokenOffsets as $collectedTokenOffset) {
                    if (!array_key_exists($index + $lookAheadOffset + $collectedTokenOffset, $tokens)) {
                        throw new ExpressionBuildingMissingOperandForFoundTokenException();
                    } else {
                        $additionalTokens[] = $tokens[$index + $lookAheadOffset + $collectedTokenOffset];
                    }
                }
                if (count($this->collectedTokenOffsets) > 1) {
                    $minCollectedIndexOffset = min(...$this->collectedTokenOffsets);
                    $maxCollectedIndexOffset = max(...$this->collectedTokenOffsets);
                } else {
                    $minCollectedIndexOffset = reset($this->collectedTokenOffsets);
                    $maxCollectedIndexOffset = reset($this->collectedTokenOffsets);
                }
                $minIndex = min($index, $index + $lookAheadOffset + $minCollectedIndexOffset);
                $maxIndex = max($index, $index + $lookAheadOffset + $maxCollectedIndexOffset);
                $extractorCallable = $this->extractor;
                $replacement = $extractorCallable($additionalTokens, $lookAheadToken);
                array_splice($tokens, $minIndex, $maxIndex - $minIndex + 1, is_array($replacement) ? $replacement : [$replacement]);
                return $tokens;
            }
        }

        return $tokens;
    }

    /**
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    private function convertTokensFromLeft(array $tokens): array
    {
        for ($index = 0; $index < count($tokens); $index++) {
            $token = $tokens[$index];
            if ($token === $this->startTrigger) {
                $lookAheadOffset = 0;
                $lookAheadToken = null;
                if ($this->lookAheadToken) {
                    $lookAheadToken = $tokens[$index + 1];
                    if ($lookAheadToken === $this->lookAheadToken) {
                        $lookAheadOffset = 1;
                    } else {
                        $lookAheadToken = null;
                    }
                }
                $additionalTokens = [];
                foreach ($this->collectedTokenOffsets as $collectedTokenOffset) {
                    if (!array_key_exists($index + $lookAheadOffset + $collectedTokenOffset, $tokens)) {
                        throw new ExpressionBuildingMissingOperandForFoundTokenException();
                    } else {
                        $additionalTokens[] = $tokens[$index + $lookAheadOffset + $collectedTokenOffset];
                    }
                }
                if (count($this->collectedTokenOffsets) > 1) {
                    $minCollectedIndexOffset = min(...$this->collectedTokenOffsets);
                    $maxCollectedIndexOffset = max(...$this->collectedTokenOffsets);
                } else {
                    $minCollectedIndexOffset = reset($this->collectedTokenOffsets);
                    $maxCollectedIndexOffset = reset($this->collectedTokenOffsets);
                }
                $minIndex = min($index, $index + $lookAheadOffset + $minCollectedIndexOffset);
                $maxIndex = max($index, $index + $lookAheadOffset + $maxCollectedIndexOffset);
                $extractorCallable = $this->extractor;
                $replacement = $extractorCallable($additionalTokens, $lookAheadToken);
                array_splice($tokens, $minIndex, $maxIndex - $minIndex + 1, is_array($replacement) ? $replacement : [$replacement]);
                return $tokens;
            }
        }

        return $tokens;
    }
}
