<?php

namespace CrazyCodeGen\Definition\Definitions\Values;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ArrayAssignToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\CommaToken;
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
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        $tokens[] = new SquareStartToken();
        $keysAllInSequentialOrder = $this->areAllKeysInNumericalSequentialOrder();
        $entriesLeft = count($this->keyValues);
        foreach ($this->keyValues as $key => $value) {
            $entriesLeft--;
            $tokens[] = $this->renderSimpleEntry(
                $context,
                key: $keysAllInSequentialOrder ? null : $key,
                value: $value,
                addSeparator: $entriesLeft !== 0,
            );
        }
        $tokens[] = new SquareEndToken();
        return $this->flatten($tokens);
    }

    /**
     * @param TokenizationContext $context
     * @param int|string|null $key
     * @param mixed $value
     * @param bool $addSeparator
     * @return Token[]
     */
    private function renderSimpleEntry(
        TokenizationContext $context,
        null|int|string $key,
        mixed           $value,
        bool            $addSeparator,
    ): array {
        $tokens = [];
        if (is_string($key)) {
            $tokens[] = (new StringVal($key))->getSimpleTokens($context);
            $tokens[] = new ArrayAssignToken();
        } elseif (!is_null($key)) {
            $tokens[] = [new Token($key)];
            $tokens[] = new ArrayAssignToken();
        }
        $valueTokens = $this->getSimpleTokensForValue($context, $value);
        $tokens = array_merge($tokens, $valueTokens);
        if ($addSeparator) {
            $tokens[] = new CommaToken();
        }
        return $tokens;
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
     * @param TokenizationContext $context
     * @param mixed $value
     * @return array
     */
    private function getSimpleTokensForValue(
        TokenizationContext $context,
        mixed $value
    ): array {
        $tokens = [];
        if (is_string($value)) {
            $tokens[] = (new StringVal($value))->getSimpleTokens($context);
        } elseif (is_bool($value)) {
            $tokens[] = new Token($value ? 'true' : 'false');
        } elseif (is_null($value)) {
            $tokens[] = new Token('null');
        } elseif ($value instanceof Tokenizes) {
            $tokens[] = $value->getSimpleTokens($context);
        } elseif ($value instanceof Token) {
            $tokens[] = $value;
        } else {
            $tokens[] = new Token($value);
        }
        return $tokens;
    }
}
