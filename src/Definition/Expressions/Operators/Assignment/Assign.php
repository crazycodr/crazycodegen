<?php

namespace CrazyCodeGen\Definition\Expressions\Operators\Assignment;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Definition\Expressions\Instruction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class Assign extends Instruction
{
    use FlattenFunction;

    public function __construct(
        public string|Token|Tokenizes $subject,
        public string|Token|Tokenizes $value,
    ) {
        if (is_string($this->subject)) {
            $this->subject = new Token($this->subject);
        }
        if (is_string($this->value)) {
            $this->value = new Token($this->value);
        }
        parent::__construct(instructions: [
            $this->subject,
            new SpacesToken(),
            new EqualToken(),
            new SpacesToken(),
            $this->value
        ]);
    }

    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return parent::getTokens($context, $rules); // TODO: Change the autogenerated stub
    }
}
