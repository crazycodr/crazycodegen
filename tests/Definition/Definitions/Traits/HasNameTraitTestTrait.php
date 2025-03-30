<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use PHPUnit\Framework\Attributes\DataProvider;

trait HasNameTraitTestTrait
{
    /**
     * @throws InvalidIdentifierFormatException
     */
    public abstract function getHasNameTraitTestObject(string $identifier): mixed;

    public static function providesClassNameMustBeAValidIdentifier(): array
    {
        return [
            'valid-alpha-identifier' => ['fooBar'],
            'valid-alphaNum-identifier' => ['fooBar123'],
            'valid-alphaUnderscore-identifier' => ['fooBar_'],
            'valid-alphaNumUnderscore-identifier' => ['foo123_'],
            'valid-alphaUnderscoreNum-identifier' => ['fooBar_123'],
            'valid-underscoreAlphaNum-identifier' => ['_fooBar123'],
            'valid-underscoreNumAlpha-identifier' => ['_123FooBar'],
            'valid-underscoreAlpha-identifier' => ['_fooBar'],
            'valid-underscoreNum-identifier' => ['_123'],
            'valid-underscore-identifier' => ['_'],
        ];
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    #[DataProvider(methodName: 'providesClassNameMustBeAValidIdentifier')]
    public function testClassNameMustBeAValidIdentifier(string $identifier): void
    {
        $tested = $this->getHasNameTraitTestObject($identifier);
        $this->assertEquals($identifier, $tested->name);
    }

    public static function providesClassNameMustThrownWhenInvalidIdentifier(): array
    {
        return [
            'invalid-num-identifier' => ['123'],
            'invalid-num-alpha-identifier' => ['123FooBar'],
            'invalid-num-underscore-identifier' => ['123_'],
            'invalid-num-underscore-alpha-identifier' => ['123_fooBar'],
        ];
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    #[DataProvider(methodName: 'providesClassNameMustThrownWhenInvalidIdentifier')]
    public function testClassNameMustThrownWhenInvalidIdentifier(string $identifier): void
    {
        $this->expectException(InvalidIdentifierFormatException::class);
        $this->getHasNameTraitTestObject($identifier);
    }
}