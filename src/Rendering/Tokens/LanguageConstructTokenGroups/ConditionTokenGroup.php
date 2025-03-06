<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLineTokens;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ElseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IfToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class ConditionTokenGroup extends TokenGroup
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        /** @var array<Token|TokenGroup>|Token|TokenGroup $condition */
        public readonly array|Token|TokenGroup $condition,
        /** @var array<Token|TokenGroup>|Token|TokenGroup $trueInstructions */
        public readonly array|Token|TokenGroup $trueInstructions,
        /** @var array<Token|TokenGroup>|Token|TokenGroup $falseInstructions */
        public readonly array|Token|TokenGroup $falseInstructions = [],
    )
    {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
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
            $tokens[] = new NewLineTokens();
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new NewLineTokens();

        $rules->indent($context);
        $trueTokens = $this->renderInstructionsFromFlexibleTokenValue($this->trueInstructions, $context, $rules);
        if (!empty($trueTokens)) {
            $tokens[] = $this->insertIndentationTokens($context, $trueTokens);
        }
        $rules->unindent($context);

        $tokens[] = new BraceEndToken();
        if (empty($this->falseInstructions)) {
            return $this->flatten($tokens);
        }
        if ($rules->conditions->keywordAfterClosingBrace === BracePositionEnum::NEXT_LINE) {
            $tokens[] = new NewLineTokens();
        } else {
            $tokens[] = new SpacesToken($rules->conditions->spacesAfterClosingBrace);
        }

        $tokens[] = new ElseToken();
        if ($this->falseInstructions instanceof ConditionTokenGroup) {
            $tokens[] = $this->falseInstructions->render($context, $rules);
        } else {
            if ($rules->conditions->openingBrace === BracePositionEnum::SAME_LINE) {
                $tokens[] = new SpacesToken($rules->conditions->spacesBeforeOpeningBrace);
            } else {
                $tokens[] = new NewLineTokens();
            }
            $tokens[] = new BraceStartToken();
            $tokens[] = new NewLineTokens();

            $rules->indent($context);
            $falseTokens = $this->renderInstructionsFromFlexibleTokenValue($this->falseInstructions, $context, $rules);
            if (!empty($falseTokens)) {
                $tokens = array_merge($tokens, $this->insertIndentationTokens($context, $falseTokens));
            }
            $rules->unindent($context);

            $tokens[] = new BraceEndToken();
        }

        return $this->flatten($tokens);
    }
}