<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use PHPUnit\Framework\Attributes\DataProvider;

trait HasImportsTraitTestTrait
{
    /**
     * @param string[]|ClassTypeDef[]|ImportDef[] $imports
     *
     * @throws InvalidIdentifierFormatException
     */
    public abstract function getHasImportsTraitTestObject(array $imports): mixed;

    public static function providesImportScenarios(): array
    {
        return [
            'empty-empty' => [[], []],
            'string-to-object' => [
                ['CrazyCodeGen\Tests\Test1'],
                [new ImportDef('CrazyCodeGen\Tests\Test1')]
            ],
            'class-to-object' => [
                [new ClassTypeDef('CrazyCodeGen\Tests\Test1')],
                [new ImportDef('CrazyCodeGen\Tests\Test1')]
            ],
            'object-to-object' => [
                [new ImportDef('CrazyCodeGen\Tests\Test1')],
                [new ImportDef('CrazyCodeGen\Tests\Test1')]
            ],
            'mixed-to-object' => [
                [
                    'CrazyCodeGen\Tests\Test1',
                    new ClassTypeDef('CrazyCodeGen\Tests\Test2'),
                    new ImportDef('CrazyCodeGen\Tests\Test3')
                ],
                [
                    new ImportDef('CrazyCodeGen\Tests\Test1'),
                    new ImportDef('CrazyCodeGen\Tests\Test2'),
                    new ImportDef('CrazyCodeGen\Tests\Test3')
                ],
            ],
        ];
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    #[DataProvider(methodName: 'providesImportScenarios')]
    public function testImportIsConvertedAsExpected(array $imports, array $expectation): void
    {
        $tested = $this->getHasImportsTraitTestObject($imports);
        $this->assertEquals($expectation, $tested->imports);
    }
}