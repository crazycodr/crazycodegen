<?php

namespace CrazyCodeGen\Tests\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\InvalidIdentifierFormatException;
use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;
use PHPUnit\Framework\Attributes\DataProvider;

trait HasNamespaceTraitTestTrait
{
    /**
     * @throws InvalidIdentifierFormatException
     */
    abstract protected function getHasNamespaceTraitTestObject(null|NamespaceDef $namespace): mixed;

    /**
     * @return array<string, mixed[]>
     */
    public static function providesNamespaceScenarios(): array
    {
        return [
            'null-to-null' => [null, null],
            'object-to-object' => [new NamespaceDef('CrazyCodeGen\Tests'), new NamespaceDef('CrazyCodeGen\Tests')],
        ];
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    #[DataProvider('providesNamespaceScenarios')]
    public function testNamespaceIsConvertedAsExpected(null|NamespaceDef $ns, null|NamespaceDef $expectation): void
    {
        $tested = $this->getHasNamespaceTraitTestObject($ns);
        $this->assertEquals($expectation, $tested->namespace);
    }
}
