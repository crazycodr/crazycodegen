<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ArrayAssignToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SquareEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SquareStartToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ArrayVal extends BaseVal
{
    use FlattenFunction;
    use TokenFunctions;
    use ValidationTrait;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function __construct(
        /** @var ProvidesClassReference[]|mixed[] $keyValues */
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
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = $this->prepareTokens(context: $context, forceMultiline: false);
        if ($context->maximumSingleLineArrayLength !== null) {
            $definitionLength = strlen($this->renderTokensToString($tokens));
            if ($definitionLength >= $context->maximumSingleLineArrayLength) {
                $tokens = $this->prepareTokens(context: $context, forceMultiline: true);
            }
        }
        return $tokens;
    }

    /**
     * @param RenderingContext $context
     * @param bool $forceMultiline
     * @return mixed[]
     */
    private function prepareTokens(RenderingContext $context, bool $forceMultiline): array
    {
        $tokens = [];
        $tokens[] = new SquareStartToken();
        $keysAllInSequentialOrder = $this->areAllKeysInNumericalSequentialOrder();
        $entriesLeft = count($this->keyValues);
        foreach ($this->keyValues as $key => $value) {
            $entriesLeft--;
            if ($forceMultiline) {
                $tokens[] = new NewLinesToken();
            }
            $tokens[] = $this->renderEntry(
                $context,
                key: $keysAllInSequentialOrder ? null : $key,
                value: $value,
                addSeparator: $entriesLeft !== 0,
            );
        }
        if ($forceMultiline) {
            $tokens[] = new NewLinesToken();
        }
        $tokens[] = new SquareEndToken();
        return $this->flatten($tokens);
    }

    /**
     * @param RenderingContext $context
     * @param int|string|null $key
     * @param mixed $value
     * @param bool $addSeparator
     * @return Token[]
     */
    private function renderEntry(
        RenderingContext $context,
        null|int|string  $key,
        mixed            $value,
        bool             $addSeparator,
    ): array {
        $tokens = [];
        if (is_string($key)) {
            $tokens[] = (new StringVal($key))->getTokens($context);
            $tokens[] = new ArrayAssignToken();
        } elseif (!is_null($key)) {
            $tokens[] = [new Token((string)$key)];
            $tokens[] = new ArrayAssignToken();
        }
        $valueTokens = $this->getTokensForValue($context, $value);
        $tokens = array_merge($tokens, $valueTokens);
        if ($addSeparator) {
            $tokens[] = new CommaToken();
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
     * @param RenderingContext $context
     * @param mixed $value
     * @return Token[]
     */
    private function getTokensForValue(
        RenderingContext $context,
        mixed            $value
    ): array {
        $tokens = [];
        if (is_string($value)) {
            $tokens[] = (new StringVal($value))->getTokens($context);
        } elseif (is_bool($value)) {
            $tokens[] = new Token($value ? 'true' : 'false');
        } elseif (is_null($value)) {
            $tokens[] = new Token('null');
        } elseif ($value instanceof Tokenizes) {
            $tokens[] = $value->getTokens($context);
        } elseif ($value instanceof Token) {
            $tokens[] = $value;
        } else {
            $tokens[] = new Token((string)$value);
        }
        return $this->flatten($tokens);
    }
}
