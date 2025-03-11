<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\IsStaticAccessContext;
use CrazyCodeGen\Rendering\Renderers\RendersChopDownVersion;
use CrazyCodeGen\Rendering\Renderers\RendersInlineVersion;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MemberAccessToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ChainTokenGroup extends TokenGroup implements RendersInlineVersion, RendersChopDownVersion
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var string|Token|TokenGroup|string[]|Token[]|TokenGroup[] $chain */
        public string|array|Token|TokenGroup $chain = [],
    ) {
        if (is_string($this->chain)) {
            $this->chain = new Token($this->chain);
        }
        if (!is_array($this->chain)) {
            $this->chain = [$this->chain];
        }
        foreach ($this->chain as $chainItemIndex => $chainItem) {
            if (is_string($chainItem)) {
                $this->chain[$chainItemIndex] = new Token($chainItem);
            } elseif ($chainItem instanceof PropertyTokenGroup) {
                $this->chain[$chainItemIndex] = new Token($chainItem->name);
            }
        }
    }

    /**
     * @return Token[]
     */
    public function renderChopDownScenario(RenderContext $context, RenderingRules $rules): array
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
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $inlineScenario = $this->renderInlineScenario($context, $rules);
        if (!$rules->exceedsAvailableSpace($context->getCurrentLine(), $this->renderTokensToString($inlineScenario))) {
            $tokens[] = $inlineScenario;
        } else {
            $tokens[] = $this->renderChopDownScenario($context, $rules);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return Token[]
     */
    public function renderInlineScenario(RenderContext $context, RenderingRules $rules): array
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
        } elseif ($chainItem instanceof TokenGroup && $chainItem instanceof RendersChopDownVersion) {
            $tokens[] = $chainItem->renderChopDownScenario($context, $rules);
        } elseif ($chainItem instanceof TokenGroup) {
            $tokens[] = $chainItem->render($context, $rules);
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
        } elseif ($chainItem instanceof TokenGroup && $chainItem instanceof RendersInlineVersion) {
            $tokens[] = $chainItem->renderInlineScenario($context, $rules);
        } elseif ($chainItem instanceof TokenGroup) {
            $tokens[] = $chainItem->render($context, $rules);
        }
        return $tokens;
    }

    private function getProperAccessToken(mixed $concerningTokenGroup): Token
    {
        if ($concerningTokenGroup instanceof IsStaticAccessContext && $concerningTokenGroup->isStaticAccessContext()) {
            return new StaticAccessToken();
        }
        return new MemberAccessToken();
    }
}
