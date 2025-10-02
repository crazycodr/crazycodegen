<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ArrayToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\BoolToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\CallableToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FalseToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\FloatToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IntToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\IterableToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\MixedToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\NullToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\ObjectToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\StringToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\TrueToken;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\VoidToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class BuiltInTypeSpec extends TypeDef
{
    public function __construct(
        public readonly BuiltInTypesEnum $type
    ) {
    }

    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        return match ($this->type) {
            // Scalar types
            BuiltInTypesEnum::int => [new IntToken()],
            BuiltInTypesEnum::float => [new FloatToken()],
            BuiltInTypesEnum::bool => [new BoolToken()],
            BuiltInTypesEnum::string => [new StringToken()],

            // Structural types
            BuiltInTypesEnum::array => [new ArrayToken()],
            BuiltInTypesEnum::object => [new ObjectToken()],
            BuiltInTypesEnum::callable => [new CallableToken()],

            // Special types
            BuiltInTypesEnum::void => [new VoidToken()],

            // Singleton types
            BuiltInTypesEnum::true => [new TrueToken()],
            BuiltInTypesEnum::false => [new FalseToken()],

            // Unit types
            BuiltInTypesEnum::null => [new NullToken()],

            // Union types
            BuiltInTypesEnum::mixed => [new MixedToken()],
            BuiltInTypesEnum::iterable => [new IterableToken()],
        };
    }
}
