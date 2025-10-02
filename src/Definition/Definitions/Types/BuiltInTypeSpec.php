<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

use CrazyCodeGen\Rendering\TokenizationContext;
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
    private static array $supportedTypes = [
        // Scalar types
        'int',
        'float',
        'bool',
        'string',

        // Structural types
        'array',
        'object',
        // 'resource' NOT SUPPORTED
        'callable',

        // Special types
        // 'never' NOT SUPPORTED
        'void',

        // Singleton types
        'true',
        'false',

        // Unit types
        'null',

        // Union types
        'mixed',
        'iterable',
    ];

    public function __construct(public string $scalarType)
    {
        if (!in_array($this->scalarType, self::$supportedTypes)) {
            $this->scalarType = 'string';
        }
    }

    public static function supports(string $type): bool
    {
        return in_array($type, self::$supportedTypes);
    }

    /**
     * @return Token[]
     */
    public function getSimpleTokens(TokenizationContext $context): array
    {
        return match ($this->scalarType) {
            // Scalar types
            'int' => [new IntToken()],
            'float' => [new FloatToken()],
            'bool' => [new BoolToken()],
            'string' => [new StringToken()],

            // Structural types
            'array' => [new ArrayToken()],
            'object' => [new ObjectToken()],
            // 'resource' NOT SUPPORTED
            'callable' => [new CallableToken()],

            // Special types
            // 'never' NOT SUPPORTED
            'void' => [new VoidToken()],

            // Singleton types
            'true' => [new TrueToken()],
            'false' => [new FalseToken()],

            // Unit types
            'null' => [new NullToken()],

            // Union types
            'mixed' => [new MixedToken()],
            'iterable' => [new IterableToken()],
        };
    }
}
