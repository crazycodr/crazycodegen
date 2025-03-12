<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Base\DefinesIfStaticallyAccessed;
use CrazyCodeGen\Definition\Base\ProvidesChopDownTokens;
use CrazyCodeGen\Definition\Base\ProvidesInlineTokens;
use CrazyCodeGen\Definition\Definitions\Contexts\MemberAccessContext;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expressions\Expression;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MemberAccessToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ChainOp extends Tokenizes implements ProvidesInlineTokens, ProvidesChopDownTokens
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var string|Token|Tokenizes|string[]|Token[]|Tokenizes[] $chain */
        public string|array|Token|Tokenizes $chain = [],
    ) {
        if (is_string($this->chain)) {
            $this->chain = new Token($this->chain);
        }
        if (!is_array($this->chain)) {
            $this->chain = [$this->chain];
        }
        foreach ($this->chain as $chainItemIndex => $chainItem) {
            if (is_string($chainItem)) {
                $this->chain[$chainItemIndex] = new Expression($chainItem);
            } elseif ($chainItem instanceof PropertyDef) {
                $this->chain[$chainItemIndex] = new Expression($chainItem->name);
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getChopDownTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $firstItemTokens = null;
        $secondItem = null;
        $previousItem = null;
        $indentedItems = [];
        $chainItemsLeft = count($this->chain);
        foreach ($this->chain as $chainItem) {
            $chainItemsLeft--;
            if ($firstItemTokens === null) {
                /**
                 * Always render the first item inline or the result is going to look weird.
                 * No one would chain calls on chop down forms off the start anyway
                 */
                $tokens[] = $firstItemTokens = $this->renderChainItemTokensInline($chainItem, $context, $rules);
                $previousItem = $chainItem;
            } elseif ($secondItem === null) {
                $tokens[] = $this->getProperAccessToken($previousItem);
                $tokens[] = $secondItem = $this->renderChainItemTokensChopDown($chainItem, $context, $rules);
                if ($chainItemsLeft > 0) {
                    $tokens[] = new NewLinesToken();
                }
                $previousItem = $chainItem;
            } else {
                $indentedItems[] = $this->getProperAccessToken($previousItem);
                $indentedItems[] = $secondItem = $this->renderChainItemTokensChopDown($chainItem, $context, $rules);
                if ($chainItemsLeft > 0) {
                    $indentedItems[] = new NewLinesToken();
                }
                $previousItem = $chainItem;
            }
        }
        $indentationBasedOnFirstItem = $this->renderTokensToString($this->flatten($firstItemTokens));
        $tokens[] = $this->insertCustomIndentationTokens($rules, strlen($indentationBasedOnFirstItem), $indentedItems);
        return $this->flatten($tokens);
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $inlineScenario = $this->getInlineTokens($context, $rules);
        if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
            $tokens[] = $inlineScenario;
        } else {
            $tokens[] = $this->getChopDownTokens($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function getInlineTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $firstItem = null;
        $previousItem = null;
        foreach ($this->chain as $chainItem) {
            if ($firstItem === null) {
                $tokens[] = $firstItem = $this->renderChainItemTokensInline($chainItem, $context, $rules);
                $previousItem = $chainItem;
            } else {
                $tokens[] = $this->getProperAccessToken($previousItem);
                $tokens[] = $this->renderChainItemTokensInline($chainItem, $context, $rules);
                $previousItem = $chainItem;
            }
        }
        return $this->flatten($tokens);
    }

    /**
     * @param mixed $chainItem
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderChainItemTokensChopDown(mixed $chainItem, RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($chainItem instanceof Token) {
            $tokens[] = $chainItem;
        } elseif ($chainItem instanceof Tokenizes && $chainItem instanceof ProvidesChopDownTokens) {
            $tokens[] = $chainItem->getChopDownTokens($context, $rules);
        } elseif ($chainItem instanceof Tokenizes) {
            $tokens[] = $chainItem->getTokens($context, $rules);
        }
        return $tokens;
    }

    /**
     * @param mixed $chainItem
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function renderChainItemTokensInline(mixed $chainItem, RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($chainItem instanceof Token) {
            $tokens[] = $chainItem;
        } elseif ($chainItem instanceof Tokenizes && $chainItem instanceof ProvidesInlineTokens) {
            $tokens[] = $chainItem->getInlineTokens($context, $rules);
        } elseif ($chainItem instanceof Tokenizes) {
            $tokens[] = $chainItem->getTokens($context, $rules);
        }
        return $tokens;
    }

    private function getProperAccessToken(mixed $concerningTokenGroup): Token
    {
        if ($concerningTokenGroup instanceof DefinesIfStaticallyAccessed && $concerningTokenGroup->shouldAccessWithStatic()) {
            return new StaticAccessToken();
        }
        return new MemberAccessToken();
    }

    public function to(string|PropertyDef|MethodDef|CallOp|MemberAccessContext $what): self
    {
        if (is_string($what)) {
            $what = new Expression($what);
        }
        $this->chain[] = $what;
        return $this;
    }
}
