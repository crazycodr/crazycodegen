<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ReturnToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class ReturnInstructionTokenGroup extends InstructionTokenGroup
{
    use FlattenFunction;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(
        /** @var Token[]|Token|TokenGroup */
        public readonly array|Token|TokenGroup $instructions,
    ) {
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function render(RenderContext $context, RenderingRules $rules): array
    {
        $tokens = [];
        $tokens[] = new ReturnToken();
        $tokens[] = new SpacesToken();
        $tokens = array_merge($tokens, parent::render($context, $rules));
        return $this->flatten($tokens);
    }
}
