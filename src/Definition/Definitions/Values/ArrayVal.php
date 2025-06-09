<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Base\ProvidesChopDownTokens;
use CrazyCodeGen\Definition\Base\ProvidesInlineTokens;
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
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ArrayVal extends BaseVal implements ProvidesInlineTokens, ProvidesChopDownTokens
{
    use FlattenFunction;
    use TokenFunctions;
    use ValidationTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        public array $keyValues = [],
    ) {
        $this->keyValues = $this->convertOrThrowForEachValues($this->keyValues, [
            new ConversionRule(
                inputType: ProvidesClassReference::class,
                filter: fn (ProvidesClassReference $value) => $value->getClassReference()
            ),
            new ConversionRule(inputType: 'mixed'),
        ]);
    }

    /**
     * @param RenderingRules $rules
     * @param array $identifierTokens
     * @param RenderContext $context
     * @param array $tokens
     * @return array
     */
    private function renderSpacesAfterIdentifier(RenderingRules $rules, array $identifierTokens, RenderContext $context, array $tokens): array
    {
        if ($rules->arrays->padIdentifiers) {
            $spacesToApply = $this->calculatePaddingSize($this->renderTokensToString($identifierTokens), $context->arrayIdentifierPaddingSize);
            $tokens[] = new SpacesToken($spacesToApply ?: $rules->arrays->spacesAfterIdentifiers);
        } else {
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterIdentifiers);
        }
        return $tokens;
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($rules->arrays->wrap === WrappingDecision::NEVER) {
            $tokens[] = $this->getInlineTokens($context, $rules);
        } elseif ($rules->arrays->wrap === WrappingDecision::ALWAYS) {
            $tokens[] = $this->getChopDownTokens($context, $rules);
        } else {
            $inlineScenario = $this->getInlineTokens($context, $rules);
            if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
                $tokens[] = $inlineScenario;
            } else {
                $tokens[] = $this->getChopDownTokens($context, $rules);
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function getInlineTokens(RenderContext $context, RenderingRules $rules): array
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
     * @return Token[]
     */
    public function getChopDownTokens(RenderContext $context, RenderingRules $rules): array
    {
        $keysAllInSequentialOrder = $this->areAllKeysInNumericalSequentialOrder();
        $keyValues = $this->keyValues;
        reset($keyValues);

        $tokens = [];
        $tokens = $this->addOpeningTokens($rules, $tokens);
        $context = $this->getAdjustedContextWithIdentifierPadding($rules, $keyValues, $context, $keysAllInSequentialOrder);
        $innerEntriesTokens = $this->getInnerEntriesTokens($keyValues, $context, $rules, $keysAllInSequentialOrder);
        $tokens = $this->convertAndFlattenEntries($innerEntriesTokens, $rules, $tokens);
        if ($rules->arrays->closingBrace !== BracePositionEnum::SAME_LINE) {
            $tokens[] = new NewLinesToken();
        }
        $tokens = $this->addClosingTokens($rules, $tokens);

        return $this->flatten($tokens);
    }

    /**
     * @param array $keyValues
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param bool $keysAllInSequentialOrder
     * @return array
     */
    private function getInnerEntriesTokens(
        array $keyValues,
        RenderContext $context,
        RenderingRules $rules,
        bool $keysAllInSequentialOrder
    ): array {
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
        }
        return $innerEntriesTokens;
    }

    /**
     * @param array $innerEntriesTokens
     * @param RenderingRules $rules
     * @param array $tokens
     * @return array
     */
    private function convertAndFlattenEntries(array $innerEntriesTokens, RenderingRules $rules, array $tokens): array
    {
        foreach ($innerEntriesTokens as $index => $innerEntryTokens) {
            if ($index === 0 && $rules->arrays->openingBrace === BracePositionEnum::SAME_LINE) {
                $tokens[] = $this->flatten($innerEntryTokens);
            } elseif ($index === 0 && $rules->arrays->openingBrace !== BracePositionEnum::SAME_LINE) {
                $tokens[] = new NewLinesToken();
                $tokens[] = $this->flatten($this->insertIndentationTokens($rules, $innerEntryTokens));
            } elseif ($index !== 0) {
                $tokens[] = new NewLinesToken();
                $tokens[] = $this->flatten($this->insertIndentationTokens($rules, $innerEntryTokens));
            }
        }
        return $tokens;
    }

    /**
     * @param RenderingRules $rules
     * @param array $keyValues
     * @param RenderContext $context
     * @param bool $keysAllInSequentialOrder
     * @return RenderContext
     */
    private function getAdjustedContextWithIdentifierPadding(
        RenderingRules $rules,
        array $keyValues,
        RenderContext $context,
        bool $keysAllInSequentialOrder
    ): RenderContext {
        if ($rules->arrays->padIdentifiers) {
            $longestIdentifier = $this->getLongestIdentifier($keyValues, $context, $rules, $keysAllInSequentialOrder);
            $context = clone $context;
            $context->arrayIdentifierPaddingSize = $longestIdentifier;
        }
        return $context;
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
            $tokens[] = $identifierTokens = (new StringVal($key))->getTokens($context, $rules);
            $tokens = $this->renderSpacesAfterIdentifier($rules, $identifierTokens, $context, $tokens);
            $tokens[] = new ArrayAssignToken();
            $tokens[] = new SpacesToken($rules->arrays->spacesAfterOperators);
        } elseif (!is_null($key)) {
            $tokens[] = $identifierTokens = [new Token($key)];
            $tokens = $this->renderSpacesAfterIdentifier($rules, $identifierTokens, $context, $tokens);
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

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param int|string|null $key
     * @return Token[]
     */
    private function renderIdentifierOnly(
        RenderContext   $context,
        RenderingRules  $rules,
        null|int|string $key,
    ): array {
        $tokens = [];
        if (is_string($key)) {
            $tokens[] = (new StringVal($key))->getTokens($context, $rules);
        } elseif (!is_null($key)) {
            $tokens[] = new Token($key);
        }
        return $this->flatten($tokens);
    }

    private function areAllKeysInNumericalSequentialOrder(): bool
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
    private function addOpeningTokens(RenderingRules $rules, array $tokens): array
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
    private function addClosingTokens(RenderingRules $rules, array $tokens): array
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
            $tokens[] = (new StringVal($value))->getTokens($context, $rules);
        } elseif (is_bool($value)) {
            $tokens[] = new Token($value ? 'true' : 'false');
        } elseif (is_null($value)) {
            $tokens[] = new Token('null');
        } elseif ($value instanceof Tokenizes) {
            $tokens[] = $value->getTokens($context, $rules);
        } elseif ($value instanceof Token) {
            $tokens[] = $value;
        } else {
            $tokens[] = new Token($value);
        }
        return $tokens;
    }

    /**
     * @param array $keyValues
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @param bool $keysAllInSequentialOrder
     * @return int
     */
    private function getLongestIdentifier(
        array $keyValues,
        RenderContext $context,
        RenderingRules $rules,
        bool $keysAllInSequentialOrder
    ): int {
        $longestIdentifier = 0;
        foreach ($keyValues as $key => $value) {
            $identifierTokens = $this->renderIdentifierOnly(
                $context,
                $rules,
                $keysAllInSequentialOrder ? null : $key,
            );
            if (empty($identifierTokens)) {
                continue;
            }
            $renderedIdentifier = $this->renderTokensToString($identifierTokens);
            $longestIdentifier = max($longestIdentifier, strlen($renderedIdentifier) + $rules->arrays->spacesAfterIdentifiers);
        }
        return $longestIdentifier;
    }
}
