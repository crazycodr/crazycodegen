<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Base\ShouldNotBeNestedIntoInstruction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\NewLinesToken;

trait HasInstructionsTrait
{
    use ValidationTrait;

    /** @var NewLinesToken[]|Tokenizes[]|ShouldNotBeNestedIntoInstruction[]|Instruction[] $instructions */
    public array $instructions = [];

    /**
     * @param NewLinesToken[]|Tokenizes[]|ShouldNotBeNestedIntoInstruction[]|Instruction[] $instructions
     * @throws NoValidConversionRulesMatchedException
     */
    public function setInstructions(array $instructions): self
    {
        $this->instructions = [];
        foreach ($instructions as $instruction) {
            $this->addInstruction($instruction);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addInstruction(NewLinesToken|Tokenizes|ShouldNotBeNestedIntoInstruction|Instruction $instruction): self
    {
        $this->instructions[] = $this->convertOrThrow($instruction, [
            new ConversionRule(inputType: NewLinesToken::class),
            new ConversionRule(inputType: ShouldNotBeNestedIntoInstruction::class),
            new ConversionRule(inputType: Instruction::class),
            new ConversionRule(inputType: Tokenizes::class, outputType: Instruction::class),
        ]);
        return $this;
    }
}
