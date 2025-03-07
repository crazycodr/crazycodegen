<?php

namespace CrazyCodeGen\Rendering\Traits;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

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
            if ($token instanceof Token && !$token instanceof NewLineTokens) {
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
                            $newTokens[] = new NewLineTokens();
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
     * @param RenderingRules $rules
     * @param Token[] $tokens
     * @return Token[]
     */
    private function insertIndentationTokens(RenderingRules $rules, array $tokens): array
    {
        $tokens = $this->splitTokensWithNewlines($tokens);
        $newTokens = [];
        $lineTokens = [];
        foreach ($tokens as $token) {
            if (empty($lineTokens) && !$token instanceof NewLineTokens) {
                $newTokens[] = SpacesToken::fromString($rules->indentation);
            }
            $newTokens[] = $token;
            if ($token instanceof NewLineTokens) {
                $lineTokens = [];
            } else {
                $lineTokens[] = $token;
            }
        }
        return $newTokens;
    }

    /**
     * @param array<Token|TokenGroup>|Token|TokenGroup $instructions
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return array
     */
    private function renderInstructionsFromFlexibleTokenValue(
        array|Token|TokenGroup $instructions,
        RenderContext          $context,
        RenderingRules         $rules
    ): array {
        $tokens = [];
        if (is_array($instructions)) {
            foreach ($instructions as $instruction) {
                if (!$instruction instanceof Token && !$instruction instanceof TokenGroup) {
                    continue;
                }
                if ($instruction instanceof NewLineTokens) {
                    $tokens[] = $instruction;
                } else {
                    $tokens[] = $this->convertFlexibleTokenValueToTokens($instruction, $context, $rules);
                    $tokens[] = new NewLineTokens();
                }
            }
        } elseif ($instructions instanceof NewLineTokens) {
            $tokens[] = $instructions;
        } elseif ($instructions instanceof Token) {
            $tokens[] = $instructions;
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLineTokens();
        } elseif ($instructions instanceof TokenGroup) {
            $tokens[] = $instructions->render($context, $rules);
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLineTokens();
        }
        return $this->flatten($tokens);
    }

    /**
     * @param array<Token|TokenGroup>|Token|TokenGroup $values
     */
    private function convertFlexibleTokenValueToTokens(
        array|Token|TokenGroup $values,
        RenderContext          $context,
        RenderingRules         $rules,
    ): array {
        $tokens = [];
        if (is_array($values)) {
            foreach ($values as $value) {
                if ($value instanceof Token) {
                    $tokens[] = $value;
                } elseif ($value instanceof TokenGroup) {
                    $tokens[] = $value->render($context, $rules);
                } elseif (is_array($value)) {
                    $tokens[] = $this->convertFlexibleTokenValueToTokens($value, $context, $rules);
                }
            }
        } elseif ($values instanceof Token) {
            $tokens[] = $values;
        } elseif ($values instanceof TokenGroup) {
            $tokens[] = $values->render($context, $rules);
        }
        return $this->flatten($tokens);
    }
}
