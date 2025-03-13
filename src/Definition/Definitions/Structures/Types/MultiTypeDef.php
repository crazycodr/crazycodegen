<?php

namespace CrazyCodeGen\Definition\Definitions\Structures\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AmpersandToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PipeToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class MultiTypeDef extends TypeDef
{
    use FlattenFunction;
    use TypeInferenceTrait;

    public function __construct(
        /** @var string[]|TypeDef[] $types */
        public array $types,
        public bool  $unionTypes = true,
        public bool  $nestedTypes = false,
    ) {
        foreach ($this->types as $typeIndex => $type) {
            if (is_null($type)) {
                $this->types[$typeIndex] = new NullToken();
            } elseif (is_string($type)) {
                $this->types[$typeIndex] = $this->inferAnyType($type);
            } elseif (!$type instanceof TypeDef) {
                unset($this->types[$typeIndex]);
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
            $tokens[] = $type->getTokens($context, $rules);
            $hasToken = true;
        }
        if ($this->nestedTypes) {
            $tokens[] = new ParEndToken();
        }
        $tokens[] = new SpacesToken($context->argumentDefinitionTypePaddingSize);
        return $this->flatten($tokens);
    }
}
