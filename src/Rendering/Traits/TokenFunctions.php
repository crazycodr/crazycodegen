<?php

namespace CrazyCodeGen\Rendering\Traits;

use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;

trait TokenFunctions
{
    /**
     * @param Token[] $tokens
     * @return Token[]
     */
    public function splitTokensWithNewlines(array $tokens): array
    {
        $newTokens = [];
        foreach ($tokens as $token) {
            if ($token instanceof Token && !$token instanceof NewLinesToken) {
                $hasNewLines = str_contains($token->text, "\n");
                if ($hasNewLines) {
                    $renderedToken = $token->render();
                    $splitTokens = array_map(
                        fn (string $token) => new Token($token),
                        explode("\n", $renderedToken)
                    );
                    $splitTokensLeft = count($splitTokens);
                    foreach ($splitTokens as $splitToken) {
                        $splitTokensLeft--;
                        $newTokens[] = $splitToken;
                        if ($splitTokensLeft > 0) {
                            $newTokens[] = new NewLinesToken();
                        }
                    }
                } else {
                    $newTokens[] = $token;
                }
            } else {
                $newTokens[] = $token;
            }
        }
        return $newTokens;
    }

    /**
     * @param Token[] $tokens
     * @return string
     */
    private function renderTokensToString(array $tokens): string
    {
        return join('', array_map(fn (Token $token) => $token->render(), $tokens));
    }

    /**
     * @param Token[] $tokens
     * @return bool
     */
    private function tokensSpanMultipleLines(array $tokens): bool
    {
        foreach ($tokens as $token) {
            if ($token instanceof NewLinesToken) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param RenderingRules $rules
     * @param int $customSize
     * @param Token[] $tokens
     * @param bool $skipFirstLine
     * @return Token[]
     */
    private function insertCustomIndentationTokens(RenderingRules $rules, int $customSize, array $tokens, bool $skipFirstLine = false): array
    {
        $backupIndentationToken = $rules->indentation;
        $rules->indentation = str_repeat(' ', $customSize);
        $tokens = $this->insertIndentationTokens($rules, $tokens, $skipFirstLine);
        $rules->indentation = $backupIndentationToken;
        return $tokens;
    }

    /**
     * @param RenderingRules $rules
     * @param Token[] $tokens
     * @param bool $skipFirstLine
     * @return Token[]
     */
    private function insertIndentationTokens(RenderingRules $rules, array $tokens, bool $skipFirstLine = false): array
    {
        $tokens = $this->splitTokensWithNewlines($tokens);
        $tokens = $this->flatten($tokens);
        $newTokensPerLine = [];
        $lineTokens = [];
        foreach ($tokens as $token) {
            if (empty($lineTokens) && !$token instanceof NewLinesToken) {
                if ($skipFirstLine === false) {
                    $lineTokens[] = SpacesToken::fromString($rules->indentation);
                } elseif ($skipFirstLine === true && !empty($newTokensPerLine)) {
                    $lineTokens[] = SpacesToken::fromString($rules->indentation);
                }
            }
            $lineTokens[] = $token;
            if ($token instanceof NewLinesToken) {
                $newTokensPerLine[] = $lineTokens;
                $lineTokens = [];
            }
        }
        if (!empty($lineTokens)) {
            $newTokensPerLine[] = $lineTokens;
        }
        return $this->flatten($newTokensPerLine);
    }

    /**
     * @param array<Token|Tokenizes>|Token|Tokenizes $instructions
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return array
     */
    private function renderInstructionsFromFlexibleTokenValue(
        array|Token|Tokenizes $instructions,
        RenderContext         $context,
        RenderingRules        $rules
    ): array {
        $tokens = [];
        if (is_array($instructions)) {
            foreach ($instructions as $instruction) {
                if (!$instruction instanceof Token && !$instruction instanceof Tokenizes) {
                    continue;
                }
                if ($instruction instanceof NewLinesToken) {
                    $tokens[] = $instruction;
                } else {
                    $tokens[] = $this->convertFlexibleTokenValueToTokens($instruction, $context, $rules);
                    $tokens[] = new NewLinesToken();
                }
            }
        } elseif ($instructions instanceof NewLinesToken) {
            $tokens[] = $instructions;
        } elseif ($instructions instanceof Token) {
            $tokens[] = $instructions;
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLinesToken();
        } elseif ($instructions instanceof Tokenizes) {
            $tokens[] = $instructions->getTokens($context, $rules);
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLinesToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @param array<Token|Tokenizes>|Token|Tokenizes $values
     */
    private function convertFlexibleTokenValueToTokens(
        array|Token|Tokenizes $values,
        RenderContext         $context,
        RenderingRules        $rules,
    ): array {
        $tokens = [];
        if (is_array($values)) {
            foreach ($values as $value) {
                if ($value instanceof Token) {
                    $tokens[] = $value;
                } elseif ($value instanceof Tokenizes) {
                    $tokens[] = $value->getTokens($context, $rules);
                } elseif (is_array($value)) {
                    $tokens[] = $this->convertFlexibleTokenValueToTokens($value, $context, $rules);
                }
            }
        } elseif ($values instanceof Token) {
            $tokens[] = $values;
        } elseif ($values instanceof Tokenizes) {
            $tokens[] = $values->getTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }
}
