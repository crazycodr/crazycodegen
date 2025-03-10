<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\EqualToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class AssignInstructionTokenGroup extends InstructionTokenGroup
{
    use FlattenFunction;

    public function __construct(
        public string|Token|TokenGroup $subject,
        public string|Token|TokenGroup $value,
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

    public function render(RenderContext $context, RenderingRules $rules): array
    {
        return parent::render($context, $rules); // TODO: Change the autogenerated stub
    }
}
