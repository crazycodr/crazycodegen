<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SemiColonToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\AsToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FunctionToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\UseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ImportFunctionDef extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public readonly FunctionDef $function;

    public function __construct(
        string|FunctionDef $function,
        public null|string         $alias = null,
    ) {
        if (is_string($function)) {
            $this->function = new FunctionDef(name: $function);
        } else {
            $this->function = $function;
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        $tokens[] = new UseToken();
        $tokens[] = new SpacesToken();
        $tokens[] = new FunctionToken();
        $tokens[] = new SpacesToken();
        if ($this->function->namespace) {
            $tokens[] = new Token($this->function->namespace->path);
        }
        $tokens[] = $this->function->getCallableReference()->getTokens($context);
        if ($this->alias) {
            $tokens[] = new SpacesToken();
            $tokens[] = new AsToken();
            $tokens[] = new SpacesToken();
            $tokens[] = new Token($this->alias);
        }
        $tokens[] = new SemiColonToken();
        return $this->flatten($tokens);
    }
}
