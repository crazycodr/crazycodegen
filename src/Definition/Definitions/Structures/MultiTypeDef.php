<?php

namespace CrazyCodeGen\Definition\Definitions\Structures;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Definition\Base\Tokenizes;
use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AmpersandToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PipeToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\SpacesToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class MultiTypeDef extends Tokenizes
{
    use FlattenFunction;

    public function __construct(
        /** @var string[]|SingleTypeDef[]|MultiTypeDef[] $types */
        public array $types,
        public bool  $unionTypes = true,
        public bool  $nestedTypes = false,
    ) {
        foreach ($this->types as $typeIndex => $type) {
            if (is_string($type)) {
                $this->types[$typeIndex] = new SingleTypeDef($type);
            } elseif (!$type instanceof SingleTypeDef && !$type instanceof MultiTypeDef) {
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
            if (is_string($type)) {
                $tokens[] = (new SingleTypeDef($type))->getTokens($context, $rules);
            } else {
                $tokens[] = $type->getTokens($context, $rules);
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
     * @return SingleTypeDef[]
     */
    public function getAllTypes(): array
    {
        $types = [];
        foreach ($this->types as $type) {
            if ($type instanceof SingleTypeDef) {
                $types[] = $type;
            } elseif ($type instanceof MultiTypeDef) {
                $types = array_merge($types, $type->getAllTypes());
            }
        }
        return $types;
    }
}
