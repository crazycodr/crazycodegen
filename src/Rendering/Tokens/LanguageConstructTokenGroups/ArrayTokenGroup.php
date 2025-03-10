<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Enums\WrappingDecision;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ArrayAssignToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SquareEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SquareStartToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ArrayToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ArrayTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public array $keyValues = [],
    ) {
    }

    /**
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens = $this->addOpeningTokens($rules, $tokens);
        $keysAllInSequentialOrder = $this->areAllKeysInNumericalSequentialOrder();
        $keyValues = $this->keyValues;
        reset($keyValues);
        if ($rules->arrays->openingBrace === BracePositionEnum::SAME_LINE) {
            $firstKey = key($keyValues);
            $firstValue = current($keyValues);
            array_shift($keyValues);
            $tokens[] = $this->renderEntry(
                context: $context,
                rules: $rules,
                key: $keysAllInSequentialOrder ? null : $firstKey,
                value: $firstValue,
                addSeparator: (
                    count($keyValues) === 0
                    && $rules->arrays->addSeparatorToLastItem
                    && $rules->arrays->closingBrace === BracePositionEnum::DIFF_LINE
                ) || count($keyValues) !== 0,
                addSpacingAfterSeparator: false
            );
            if (count($keyValues) > 0) {
                $tokens[] = new NewLinesToken();
            }
        } else {
            $tokens[] = new NewLinesToken();
        }
        $entriesLeft = count($keyValues);
        $innerEntriesTokens = [];
        foreach ($keyValues as $key => $value) {
            $entriesLeft--;
            $innerEntriesTokens[] = $this->renderEntry(
                context: $context,
                rules: $rules,
                key: $keysAllInSequentialOrder ? null : $key,
                value: $value,
                addSeparator: (
                    $entriesLeft === 0
                    && $rules->arrays->addSeparatorToLastItem
                    && $rules->arrays->closingBrace === BracePositionEnum::DIFF_LINE
                ) || $entriesLeft !== 0,
                addSpacingAfterSeparator: false,
            );
            if ($entriesLeft > 0) {
                $innerEntriesTokens[] = new NewLinesToken();
            }
        }
        $tokens = array_merge($tokens, $this->insertIndentationTokens($rules, $innerEntriesTokens));
        if ($rules->arrays->closingBrace !== BracePositionEnum::SAME_LINE) {
            $tokens[] = new NewLinesToken();
        }
        $tokens = $this->addClosingTokens($rules, $tokens);
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($rules->arrays->wrap === WrappingDecision::NEVER) {
            $tokens[] = $this->renderInlineScenario($context, $rules);
        } elseif ($rules->arrays->wrap === WrappingDecision::ALWAYS) {
            $tokens[] = $this->renderChopDownScenario($context, $rules);
        } else {
            $inlineScenario = $this->renderInlineScenario($context, $rules);
            if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
                $tokens[] = $inlineScenario;
            } else {
                $tokens[] = $this->renderChopDownScenario($context, $rules);
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderInlineScenario(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens = $this->addOpeningTokens($rules, $tokens);
        $keysAllInSequentialOrder = $this->areAllKeysInNumericalSequentialOrder();
        $entriesLeft = count($this->keyValues);
        foreach ($this->keyValues as $key => $value) {
            $entriesLeft--;
            $tokens[] = $this->renderEntry(
                context: $context,
                rules: $rules,
                key: $keysAllInSequentialOrder ? null : $key,
                value: $value,
                addSeparator: $entriesLeft !== 0,
                addSpacingAfterSeparator: true,
            );
        }
        $tokens = $this->addClosingTokens($rules, $tokens);
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param int|string|null $key
     * @param mixed $value
     * @param bool $addSeparator
     * @param bool $addSpacingAfterSeparator
     * @return Token[]
     */
    private function renderEntry(
        RenderContext   $context,
        RenderingRules  $rules,
        null|int|string $key,
        mixed           $value,
        bool            $addSeparator,
        bool            $addSpacingAfterSeparator
    ): array {
        $tokens = [];
        if (is_string($key)) {
            $tokens[] = (new StringTokenGroup($key))->render($context, $rules);
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterIdentifiers);
            $tokens[] = new ArrayAssignToken();
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterOperators);
        } elseif (!is_null($key)) {
            $tokens[] = new Token($key);
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterIdentifiers);
            $tokens[] = new ArrayAssignToken();
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterOperators);
        }
        $valueTokens = $this->getTokensForValue($value, $context, $rules);
        if ($this->tokensSpanMultipleLines($valueTokens)) {
            $valueTokens = $this->insertIndentationTokens($rules, $valueTokens, skipFirstLine: true);
        }
        $tokens = array_merge($tokens, $valueTokens);
        if ($addSeparator) {
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterValues);
            $tokens[] = new CommaToken();
        }
        if ($addSeparator && $addSpacingAfterSeparator) {
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterSeparators);
        }
        return $tokens;
    }

    public function areAllKeysInNumericalSequentialOrder(): bool
    {
        $keys = array_keys($this->keyValues);
        $keysAllInSequentialOrder = true;
        $nextExpectedKey = 0;
        foreach ($keys as $key) {
            if ($key !== $nextExpectedKey) {
                $keysAllInSequentialOrder = false;
                break;
            }
            $nextExpectedKey++;
        }
        return $keysAllInSequentialOrder;
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addOpeningTokens(RenderingRules $rules, array $tokens): array
    {
        if ($rules->arrays->useShortForm) {
            $tokens[] = new SquareStartToken();
        } else {
            $tokens[] = new ArrayToken();
            $tokens[] = new ParStartToken();
        }
        return $tokens;
    }

    /**
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    public function addClosingTokens(RenderingRules $rules, array $tokens): array
    {
        if ($rules->arrays->useShortForm) {
            $tokens[] = new SquareEndToken();
        } else {
            $tokens[] = new ParEndToken();
        }
        return $tokens;
    }

    /**
     * @param mixed $value
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return array
     */
    private function getTokensForValue(mixed $value, RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if (is_string($value)) {
            $tokens[] = (new StringTokenGroup($value))->render($context, $rules);
        } elseif (is_bool($value)) {
            $tokens[] = new Token($value ? 'true' : 'false');
        } elseif (is_null($value)) {
            $tokens[] = new Token('null');
        } elseif ($value instanceof TokenGroup) {
            $tokens[] = $value->render($context, $rules);
        } elseif ($value instanceof Token) {
            $tokens[] = $value;
        } else {
            $tokens[] = new Token($value);
        }
        return $tokens;
    }
}
