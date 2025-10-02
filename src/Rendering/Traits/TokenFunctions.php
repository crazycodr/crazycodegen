<?php

namespace CrazyCodeGen\Rendering\Traits;

use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
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
     * @param RenderingContext $context
     * @param array<Token|Tokenizes>|Token|Tokenizes $instructions
     * @return array<Token>
     */
    private function renderInstructionsFromFlexibleTokenValue(
        RenderingContext      $context,
        array|Token|Tokenizes $instructions,
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
                    $tokens = array_merge($tokens, $this->convertFlexibleTokenValueToTokens($context, $instruction));
                }
            }
        } elseif ($instructions instanceof NewLinesToken) {
            $tokens[] = $instructions;
        } elseif ($instructions instanceof Token) {
            $tokens[] = $instructions;
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLinesToken();
        } elseif ($instructions instanceof Tokenizes) {
            $tokens[] = $instructions->getTokens($context);
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLinesToken();
        }
        return $this->flatten($tokens);
    }

    /**
     * @param array<Token|Tokenizes>|Token|Tokenizes $values
     * @return array<Token>
     */
    private function convertFlexibleTokenValueToTokens(
        RenderingContext      $context,
        array|Token|Tokenizes $values,
    ): array {
        $tokens = [];
        if (is_array($values)) {
            foreach ($values as $value) {
                if ($value instanceof Token) {
                    $tokens[] = $value;
                } elseif ($value instanceof Tokenizes) {
                    $tokens[] = $value->getTokens($context);
                } elseif (is_array($value)) {
                    $tokens[] = $this->convertFlexibleTokenValueToTokens($context, $value);
                }
            }
        } elseif ($values instanceof Token) {
            $tokens[] = $values;
        } elseif ($values instanceof Tokenizes) {
            $tokens[] = $values->getTokens($context);
        }
        return $this->flatten($tokens);
    }
}
