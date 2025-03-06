<?php

namespace CrazyCodeGen\Rendering\Tokens\LanguageConstructTokenGroups;

use CrazyCodeGen\Rendering\Renderers\ContextTypeEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\RenderingRules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AmpersandToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PipeToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Definition\Traits\FlattenFunction;

class MultiTypeTokenGroup extends AbstractTypeTokenGroup
{
    use FlattenFunction;

    public function __construct(
        /** @var string[]|AbstractTypeTokenGroup[] $types */
        public array $types,
        public bool  $unionTypes = true,
        public bool  $nestedTypes = false,
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
        if ($this->nestedTypes) {
            $tokens[] = new ParStartToken();
        }
        $hasToken = false;
        foreach ($this->types as $type) {
            if ($hasToken) {
                if ($this->unionTypes) {
                    $tokens[] = new PipeToken();
                } else {
                    $tokens[] = new AmpersandToken();
                }
            }
            if (is_string($type)) {
                $tokens[] = (new SingleTypeTokenGroup($type))->render($context, $rules);
            } else {
                $tokens[] = $type->render($context, $rules);
            }
            $hasToken = true;
        }
        if ($this->nestedTypes) {
            $tokens[] = new ParEndToken();
        }
        if ($context->argumentDefinitionTypePaddingSize > 0) {
            $tokens[] = new SpacesToken($context->argumentDefinitionTypePaddingSize);
        }
        return $this->flatten($tokens);
    }

    /**
     * @return ContextTypeEnum[]
     */
    public function getContexts(): array
    {
        return array_merge([
            ContextTypeEnum::multiType,
        ], parent::getContexts());
    }
}