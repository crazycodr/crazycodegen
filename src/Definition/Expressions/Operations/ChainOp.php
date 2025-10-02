<?php

namespace CrazyCodeGen\Definition\Expressions\Operations;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Definitions\Contexts\MemberAccessContext;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;
use CrazyCodeGen\Definition\Expression;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MemberAccessToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\StaticAccessToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ChainOp extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    /**
     * @var Tokenizes[]|MemberAccessContext[]
     */
    public array $chain = [];

    /**
     * @param string[]|PropertyDef[]|Tokenizes[]|MemberAccessContext[] $chain
     */
    public function __construct(array $chain = [])
    {
        $finalChain = [];
        foreach ($chain as $chainItemIndex => $chainItem) {
            if (is_string($chainItem)) {
                $finalChain[$chainItemIndex] = new Expression($chainItem);
            } elseif ($chainItem instanceof PropertyDef) {
                $finalChain[$chainItemIndex] = new Expression($chainItem->name);
            } else {
                $finalChain[$chainItemIndex] = $chainItem;
            }
        }
        $this->chain = $finalChain;
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $firstItem = null;
        $previousItem = null;
        foreach ($this->chain as $chainItem) {
            if ($firstItem === null) {
                $tokens[] = $firstItem = $this->renderChainItemTokens($context, $chainItem);
            } else {
                $tokens[] = $this->getProperAccessToken($previousItem);
                $tokens[] = $this->renderChainItemTokens($context, $chainItem);
            }
            $previousItem = $chainItem;
        }
        return $this->flatten($tokens);
    }

    /**
     * @param RenderingContext $context
     * @param mixed $chainItem
     * @return Token[]
     */
    public function renderChainItemTokens(RenderingContext $context, mixed $chainItem): array
    {
        if ($chainItem instanceof Token) {
            return [$chainItem];
        } elseif ($chainItem instanceof Tokenizes) {
            return $chainItem->getTokens($context);
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
