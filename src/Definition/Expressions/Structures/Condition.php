<?php

namespace CrazyCodeGen\Definition\Expressions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\ProvidesVariableReference;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\TokenizationContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\BraceStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
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
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        $tokens = [];

        if (!is_null($this->condition)) {
            $tokens[] = new IfToken();
            $tokens[] = new ParStartToken();
            $tokens[] = $this->convertSimpleFlexibleTokenValueToTokens($context, $this->condition);
            $tokens[] = new ParEndToken();
        }
        $tokens[] = new BraceStartToken();

        foreach ($this->instructions as $instruction) {
            $tokens[] = $instruction->getSimpleTokens($context);
        }

        $tokens[] = new BraceEndToken();
        return $this->flatten($tokens);
    }
}
