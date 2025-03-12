<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ElseToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ConditionChain extends Tokenizes
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var Condition[] $chain */
        public array $chain = [],
    ) {
        foreach ($this->chain as $chainItemIndex => $chainItem) {
            if (!$chainItem instanceof Condition) {
                unset($this->chain[$chainItemIndex]);
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $conditionsLeft = count($this->chain);
        foreach ($this->chain as $condition) {
            $conditionsLeft--;
            $tokens[] = $condition->getTokens($context, $rules);
            if ($conditionsLeft > 0) {
                if ($rules->conditions->closingBrace === BracePositionEnum::DIFF_LINE) {
                    $tokens[] = new NewLinesToken();
                } else {
                    $tokens[] = new SpacesToken($rules->conditions->spacesAfterClosingBrace);
                }
                $tokens[] = new ElseToken();
            }
        }
        return $this->flatten($tokens);
    }
}
