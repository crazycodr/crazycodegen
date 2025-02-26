<?php

namespace CrazyCodeGen\Factories;

use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Definitions\Values\BoolValue;
use CrazyCodeGen\Definitions\Values\FloatValue;
use CrazyCodeGen\Definitions\Values\IntValue;
use CrazyCodeGen\Definitions\Values\StringValue;
use CrazyCodeGen\Exceptions\ExpressionBuildingMissingOperandForFoundTokenException;
use CrazyCodeGen\Exceptions\ExpressionBuildingYieldsMultipleFinalComponentsException;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Adds;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Divs;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Exps;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Mods;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Mults;
use CrazyCodeGen\Expressions\Operators\Arithmetics\Subs;
use CrazyCodeGen\Expressions\Operators\Comparisons\Equals;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsGreaterThan;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsGreaterThanOrEqualTo;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsLessThan;
use CrazyCodeGen\Expressions\Operators\Comparisons\IsLessThanOrEqualTo;
use CrazyCodeGen\Expressions\Operators\Comparisons\NotEquals;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Ands;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Nots;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Ors;
use CrazyCodeGen\Expressions\Operators\LogicalOperators\Xors;
use CrazyCodeGen\Expressions\Operators\Strings\Concats;
use CrazyCodeGen\Expressions\Structures\Wraps;

class ExpressionBuilder
{
    /**
     * @var UnpairedTokenConverter[]
     */
    protected array $tokenConverters = [];

    public function __construct()
    {
        $this->tokenConverters[] = new TokenConverterGroup([
            new PairedTokenConverter(
                '(',
                ')',
                fn (array $tokens) => new Wraps($tokens[0]),
                fn (array $tokens) => $this->build(...$tokens),
            ),
        ]);
        $this->tokenConverters[] = new TokenConverterGroup(
            [
                new UnpairedTokenConverter(
                    '**',
                    [-1, +1],
                    fn (array $tokens) => new Exps($tokens[0], $tokens[1]),
                    useRightAssociativity: true
                ),
            ],
            useRightAssociativity: true,
        );
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '!',
                [+1],
                fn (array $tokens, null|string $lookAheadToken) => new Nots($tokens[0], (bool)$lookAheadToken),
                '!'
            ),
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '*',
                [-1, +1],
                fn (array $tokens) => new Mults($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '/',
                [-1, +1],
                fn (array $tokens) => new Divs($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '%',
                [-1, +1],
                fn (array $tokens) => new Mods($tokens[0], $tokens[1])
            ),
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '+',
                [-1, +1],
                fn (array $tokens) => new Adds($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '-',
                [-1, +1],
                fn (array $tokens) => new Subs($tokens[0], $tokens[1])
            ),
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '.',
                [-1, +1],
                fn (array $tokens) => new Concats($tokens[0], $tokens[1])
            )
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '<',
                [-1, +1],
                fn (array $tokens) => new IsLessThan($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '<=',
                [-1, +1],
                fn (array $tokens) => new IsLessThanOrEqualTo($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '>',
                [-1, +1],
                fn (array $tokens) => new IsGreaterThan($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '>=',
                [-1, +1],
                fn (array $tokens) => new IsGreaterThanOrEqualTo($tokens[0], $tokens[1])
            ),
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '==',
                [-1, +1],
                fn (array $tokens) => new Equals($tokens[0], $tokens[1], soft: true)
            ),
            new UnpairedTokenConverter(
                '!=',
                [-1, +1],
                fn (array $tokens) => new NotEquals($tokens[0], $tokens[1], soft: true)
            ),
            new UnpairedTokenConverter(
                '===',
                [-1, +1],
                fn (array $tokens) => new Equals($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '!==',
                [-1, +1],
                fn (array $tokens) => new NotEquals($tokens[0], $tokens[1])
            ),
            new UnpairedTokenConverter(
                '<>',
                [-1, +1],
                fn (array $tokens) => new NotEquals($tokens[0], $tokens[1], useLtGt: true)
            ),
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '&&',
                [-1, +1],
                fn (array $tokens) => new Ands($tokens[0], $tokens[1])
            )
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                '||',
                [-1, +1],
                fn (array $tokens) => new Ors($tokens[0], $tokens[1])
            )
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                'and',
                [-1, +1],
                fn (array $tokens) => new Ands($tokens[0], $tokens[1], textBased: true)
            )
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                'xor',
                [-1, +1],
                fn (array $tokens) => new Xors($tokens[0], $tokens[1])
            )
        ]);
        $this->tokenConverters[] = new TokenConverterGroup([
            new UnpairedTokenConverter(
                'or',
                [-1, +1],
                fn (array $tokens) => new Ors($tokens[0], $tokens[1], textBased: true)
            )
        ]);
    }

    /**
     * @throws ExpressionBuildingYieldsMultipleFinalComponentsException
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public function build(...$tokens): CanBeComputed
    {
        $tokens = $this->convertUsingTokenConverters($tokens);
        $tokens = $this->convertValueComponents($tokens);
        if (count($tokens) !== 1) {
            throw new ExpressionBuildingYieldsMultipleFinalComponentsException();
        }
        return $tokens[0];
    }

    /**
     * @param array $components
     * @return array
     */
    public function convertValueComponents(array $components): array
    {
        foreach ($components as $index => $component) {
            if (is_int($component)) {
                $components[$index] = new IntValue($component);
            } elseif (is_float($component)) {
                $components[$index] = new FloatValue($component);
            } elseif (is_string($component)) {
                $components[$index] = new StringValue($component);
            } elseif (is_bool($component)) {
                $components[$index] = new BoolValue($component);
            }
        }
        return $components;
    }

    /**
     * @param array $tokens
     * @return array
     * @throws ExpressionBuildingMissingOperandForFoundTokenException
     */
    public function convertUsingTokenConverters(array $tokens): array
    {
        foreach ($this->tokenConverters as $tokenConverter) {
            $tokens = $tokenConverter->convertTokens($tokens);
        }
        return $tokens;
    }
}