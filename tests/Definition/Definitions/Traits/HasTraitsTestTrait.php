<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\UseTraitDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use PHPUnit\Framework\Attributes\DataProvider;

trait HasTraitsTestTrait
{
    /**
     * @throws InvalidIdentifierFormatException
     */
    abstract public function getHasTraitsTraitTestObject(string|ClassTypeDef|UseTraitDef $trait): mixed;

    /**
     * @return array<string, mixed[]>
     */
    public static function providesTraitScenarios(): array
    {
        return [
            'string-to-object' => ['CrazyCodeGen\Tests\TraitA', new UseTraitDef('CrazyCodeGen\Tests\TraitA')],
            'class-type-def-to-object' => [new ClassTypeDef('CrazyCodeGen\Tests\TraitA'), new UseTraitDef('CrazyCodeGen\Tests\TraitA')],
            'object-to-object' => [new UseTraitDef('CrazyCodeGen\Tests\TraitA'), new UseTraitDef('CrazyCodeGen\Tests\TraitA')],
        ];
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    #[DataProvider('providesTraitScenarios')]
    public function testTraitIsConvertedAsExpected(string|ClassTypeDef|UseTraitDef $input, UseTraitDef $expectation): void
    {
        $tested = $this->getHasTraitsTraitTestObject($input);
        $this->assertEquals($expectation, $tested->traits[0]);
    }
}
