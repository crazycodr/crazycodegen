<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\AmpersandToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParEndToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\ParStartToken;
use CrazyCodeGen\Rendering\Tokens\CharacterTokens\PipeToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class MultiTypeDef extends TypeDef
{
    use FlattenFunction;
    use TypeInferenceTrait;

    /** @var array<NullToken|TypeDef> */
    public array $types = [];

    /**
     * @param array<null|string|TypeDef> $types
     * @param bool $unionTypes
     * @param bool $nestedTypes
     */
    public function __construct(
        array $types,
        public bool  $unionTypes = true,
        public bool  $nestedTypes = false,
    ) {
        foreach ($types as $typeIndex => $type) {
            if (is_null($type)) {
                $this->types[$typeIndex] = new NullToken();
            } elseif (is_string($type)) {
                $this->types[$typeIndex] = $this->inferType($type);
            } elseif ($type instanceof TypeDef) {
                $this->types[$typeIndex] = $type;
            }
        }
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
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
            if ($type instanceof NullToken) {
                $tokens[] = $type;
            } else {
                $tokens[] = $type->getTokens($context);
            }
            $hasToken = true;
        }
        if ($this->nestedTypes) {
            $tokens[] = new ParEndToken();
        }
        return $this->flatten($tokens);
    }
}
