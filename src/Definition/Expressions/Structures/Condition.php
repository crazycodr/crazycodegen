<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Defines;
use CrazyCodeGen\Definition\Base\ProvidesReference;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ElseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IfToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class Condition extends Defines
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var array<Token|Defines>|Token|Defines $condition */
        public array|Token|Defines $condition,
        /** @var array<Token|Defines>|Token|Defines $trueInstructions */
        public array|Token|Defines $trueInstructions,
        /** @var array<Token|Defines>|Token|Defines $falseInstructions */
        public array|Token|Defines $falseInstructions = [],
    ) {
        if ($this->condition instanceof ProvidesReference) {
            $this->condition = $this->condition->getReference();
        }
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];

        $tokens[] = new IfToken();
        $tokens[] = new SpacesToken($rules->conditions->spacesAfterKeyword);
        $tokens[] = new ParStartToken();
        $tokens[] = $this->convertFlexibleTokenValueToTokens($this->condition, $context, $rules);
        $tokens[] = new ParEndToken();

        if ($rules->conditions->openingBrace === BracePositionEnum::SAME_LINE) {
            $tokens[] = new SpacesToken($rules->conditions->spacesBeforeOpeningBrace);
        } else {
            $tokens[] = new NewLinesToken();
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new NewLinesToken();

        $rules->indent($context);
        $trueTokens = $this->renderInstructionsFromFlexibleTokenValue($this->trueInstructions, $context, $rules);
        if (!empty($trueTokens)) {
            $tokens[] = $this->insertIndentationTokens($rules, $trueTokens);
        }
        $rules->unindent($context);

        $tokens[] = new BraceEndToken();
        if (empty($this->falseInstructions)) {
            return $this->flatten($tokens);
        }
        if ($rules->conditions->keywordAfterClosingBrace === BracePositionEnum::DIFF_LINE) {
            $tokens[] = new NewLinesToken();
        } else {
            $tokens[] = new SpacesToken($rules->conditions->spacesAfterClosingBrace);
        }

        $tokens[] = new ElseToken();
        if ($this->falseInstructions instanceof Condition) {
            $tokens[] = $this->falseInstructions->getTokens($context, $rules);
        } else {
            if ($rules->conditions->openingBrace === BracePositionEnum::SAME_LINE) {
                $tokens[] = new SpacesToken($rules->conditions->spacesBeforeOpeningBrace);
            } else {
                $tokens[] = new NewLinesToken();
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLinesToken();

            $rules->indent($context);
            $falseTokens = $this->renderInstructionsFromFlexibleTokenValue($this->falseInstructions, $context, $rules);
            if (!empty($falseTokens)) {
                $tokens = array_merge($tokens, $this->insertIndentationTokens($rules, $falseTokens));
            }
            $rules->unindent($context);

            $tokens[] = new BraceEndToken();
        }

        return $this->flatten($tokens);
    }
}
