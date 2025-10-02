<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Definitions\Contexts\MemberAccessContext;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MemberAccessToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ChainOp extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public array $chain = [],
    ) {
        foreach ($this->chain as $chainItemIndex => $chainItem) {
            if (is_string($chainItem)) {
                $this->chain[$chainItemIndex] = new Expression($chainItem);
            } elseif ($chainItem instanceof PropertyDef) {
                $this->chain[$chainItemIndex] = new Expression($chainItem->name);
            } elseif (!$chainItem instanceof Tokenizes) {
                unset($this->chain[$chainItemIndex]);
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];
        $firstItem = null;
        $previousItem = null;
        foreach ($this->chain as $chainItem) {
            if ($firstItem === null) {
                $tokens[] = $firstItem = $this->renderSimpleChainItemTokens($context, $chainItem);
            } else {
                $tokens[] = $this->getProperAccessToken($previousItem);
                $tokens[] = $this->renderSimpleChainItemTokens($context, $chainItem);
            }
            $previousItem = $chainItem;
        }
        return $this->flatten($tokens);
    }

    /**
     * @param TokenizationContext $context
     * @param mixed $chainItem
     * @return Token[]
     */
    public function renderSimpleChainItemTokens(TokenizationContext $context, mixed $chainItem): array
    {
        if ($chainItem instanceof Token) {
            return [$chainItem];
        } elseif ($chainItem instanceof Tokenizes) {
            return $chainItem->getSimpleTokens($context);
        }
        return [];
    }

    private function getProperAccessToken(mixed $concerningTokenGroup): Token
    {
        if ($concerningTokenGroup instanceof ShouldBeAccessedStatically && $concerningTokenGroup->isAccessedStatically()) {
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
