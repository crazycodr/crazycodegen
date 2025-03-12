<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MultiLineCloseCommentToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MultiLineOpenCommentToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SingleLineOpenCommentToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class Comment extends Tokenizes implements ShouldNotBeNestedIntoInstruction
{
    use FlattenFunction;

    public function __construct(
        public string $text,
        public bool   $useMultiline = false,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        if ($this->useMultiline) {
            $tokens[] = new MultiLineOpenCommentToken();
            $tokens[] = new SpacesToken();
        } else {
            $tokens[] = new SingleLineOpenCommentToken();
            $tokens[] = new SpacesToken();
        }
        $tokens[] = new Token(trim($this->text));
        if ($this->useMultiline) {
            $tokens[] = new SpacesToken();
            $tokens[] = new MultiLineCloseCommentToken();
        }
        return $this->flatten($tokens);
    }
}
