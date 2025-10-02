<?php

namespace CrazyCodeGen\Definition\Expressions;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MultiLineCloseCommentToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\MultiLineOpenCommentToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
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
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        $tokens = [];
        if ($this->useMultiline) {
            $tokens[] = new MultiLineOpenCommentToken();
        } else {
            $tokens[] = new SingleLineOpenCommentToken();
        }
        $tokens[] = new SpacesToken();
        $tokens[] = new Token(trim($this->text));
        if ($this->useMultiline) {
            $tokens[] = new SpacesToken();
            $tokens[] = new MultiLineCloseCommentToken();
        } else {
            $tokens[] = new NewLinesToken();
        }
        return $this->flatten($tokens);
    }
}
