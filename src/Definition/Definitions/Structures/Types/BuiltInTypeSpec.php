<?php

namespace CrazyCodeGen\Definition\Definitions\Structures\Types;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;
use CrazyCodeGen\Rendering\Renderers\Rules\RenderingRules;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ArrayToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\BoolToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\CallableToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FalseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FloatToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IntToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StringToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\TrueToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class BuiltInTypeSpec extends TypeDef
{
    public function __construct(public string $scalarType)
    {
        if (!in_array($this->scalarType, ['int', 'float', 'bool', 'string', 'array', 'callable', 'true', 'false'])) {
            $this->scalarType = 'string';
        }
    }

    /**
     * @param RenderContext $context
     * @param RenderingRules $rules
     * @return Token[]
     */
    public function getTokens(RenderContext $context, RenderingRules $rules): array
    {
        return match ($this->scalarType) {
            'int' => [new IntToken()],
            'float' => [new FloatToken()],
            'bool' => [new BoolToken()],
            'string' => [new StringToken()],
            'array' => [new ArrayToken()],
            'callable' => [new CallableToken()],
            'true' => [new TrueToken()],
            'false' => [new FalseToken()],
        };
    }
}
