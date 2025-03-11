<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AmpersandToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PipeToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;
use CrazyCodeGen\Rendering\Tokens\TokenGroup;

class MultiTypeDefinition extends TokenGroup
{
    use FlattenFunction;

    public function __construct(
        /** @var string[]|SingleTypeDefinition[]|MultiTypeDefinition[] $types */
        public array $types,
        public bool  $unionTypes = true,
        public bool  $nestedTypes = false,
    ) {
        foreach ($this->types as $typeIndex => $type) {
            if (is_string($type)) {
                $this->types[$typeIndex] = new SingleTypeDefinition($type);
            } elseif (!$type instanceof SingleTypeDefinition && !$type instanceof MultiTypeDefinition) {
                unset($this->types[$typeIndex]);
            }
        }
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
                $tokens[] = (new SingleTypeDefinition($type))->render($context, $rules);
            } else {
                $tokens[] = $type->render($context, $rules);
            }
            $hasToken = true;
        }
        if ($this->nestedTypes) {
            $tokens[] = new ParEndToken();
        }
        $tokens[] = new SpacesToken($context->argumentDefinitionTypePaddingSize);
        return $this->flatten($tokens);
    }

    /**
     * @return SingleTypeDefinition[]
     */
    public function getAllTypes(): array
    {
        $types = [];
        foreach ($this->types as $type) {
            if ($type instanceof SingleTypeDefinition) {
                $types[] = $type;
            } elseif ($type instanceof MultiTypeDefinition) {
                $types = array_merge($types, $type->getAllTypes());
            }
        }
        return $types;
    }
}
