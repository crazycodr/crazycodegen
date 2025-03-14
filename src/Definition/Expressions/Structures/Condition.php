<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Enums\BracePositionEnum;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IfToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Traits\TokenFunctions;

class Condition extends Tokenizes implements ShouldNotBeNestedIntoInstruction
{
    use FlattenFunction;
    use TokenFunctions;

    public function __construct(
        public null|Tokenizes $condition = null,
        /** @var Tokenizes[] $instructions */
        public array          $instructions = [],
    ) {
        if ($this->condition instanceof ProvidesVariableReference) {
            $this->condition = $this->condition->getVariableReference();
        }
        foreach ($this->instructions as $instructionIndex => $instruction) {
            if (!$instruction instanceof Instruction) {
                $this->instructions[$instructionIndex] = new Instruction([$instruction]);
            }
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

        if (!is_null($this->condition)) {
            $tokens[] = new IfToken();
            $tokens[] = new SpacesToken($rules->conditions->spacesAfterKeyword);
            $tokens[] = new ParStartToken();
            $tokens[] = $this->convertFlexibleTokenValueToTokens($this->condition, $context, $rules);
            $tokens[] = new ParEndToken();
        }

        if ($rules->conditions->openingBrace === BracePositionEnum::SAME_LINE) {
            $tokens[] = new SpacesToken($rules->conditions->spacesBeforeOpeningBrace);
        } else {
            $tokens[] = new NewLinesToken();
        }
        $tokens[] = new BraceStartToken();
        $tokens[] = new NewLinesToken();

        $rules->indent($context);
        $instructionTokens = [];
        foreach ($this->instructions as $instruction) {
            $instructionTokens[] = $instruction->getTokens($context, $rules);
            $instructionTokens[] = new NewLinesToken();
        }
        if (!empty($instructionTokens)) {
            $tokens[] = $this->insertIndentationTokens($rules, $instructionTokens);
        }
        $rules->unindent($context);

        $tokens[] = new BraceEndToken();
        return $this->flatten($tokens);
    }
}
