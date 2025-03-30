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
    public abstract function getHasNamespaceTraitTestObject(null|string|NamespaceDef $namespace): mixed;

    public static function providesNamespaceScenarios(): array
    {
        return [
            'null-to-null' => [null, null],
            'string-to-object' => ['CrazyCodeGen\Tests', new NamespaceDef('CrazyCodeGen\Tests')],
            'object-to-object' => [new NamespaceDef('CrazyCodeGen\Tests'), new NamespaceDef('CrazyCodeGen\Tests')],
        ];
    }

    /**
     * @throws InvalidIdentifierFormatException
     * @throws NoValidConversionRulesMatchedException
     */
    #[DataProvider(methodName: 'providesNamespaceScenarios')]
    public function testNamespaceIsConvertedAsExpected(null|string|NamespaceDef $ns, null|NamespaceDef $expectation): void
    {
        $tested = $this->getHasNamespaceTraitTestObject($ns);
        $this->assertEquals($expectation, $tested->namespace);
    }
}