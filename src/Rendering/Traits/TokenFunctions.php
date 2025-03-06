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
     * @return string
     */
    private function renderTokensToString(array $tokens): string
    {
        return join('', array_map(fn (Token $token) => $token->render(), $tokens));
    }

    /**
     * @param RenderContext $context
     * @param Token[] $tokens
     * @return Token[]
     */
    private function insertIndentationTokens(RenderContext $context, array $tokens): array
    {
        if (strlen($context->indents) === 0) {
            return $tokens;
        }
        $tokensLeft = count($tokens);
        $newTokens = [];
        $newTokens[] = SpacesToken::fromString($context->indents);
        foreach ($tokens as $token) {
            $tokensLeft--;
            $newTokens[] = $token;
            if ($token instanceof NewLineTokens && $tokensLeft > 0) {
                $newTokens[] = SpacesToken::fromString($context->indents);
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
                $tokens[] = $this->convertFlexibleTokenValueToTokens($instruction, $context, $rules);
                $tokens[] = new NewLineTokens();
            }
        } elseif ($instructions instanceof Token) {
            $tokens[] = $instructions;
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLineTokens();
        } elseif ($instructions instanceof TokenGroup) {
            $tokens[] = $instructions->render($context, $rules);
            $tokens[] = new SemiColonToken();
            $tokens[] = new NewLineTokens();
        }
        return $tokens;
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
        return $tokens;
    }
}
