<?php

namespace CrazyCodeGen\Tests\Common\Traits;

use CrazyCodeGen\Common\Traits\FlattenFunction;
use PHPUnit\Framework\TestCase;
use stdClass;

class FlattenFunctionTest extends TestCase
{
    use FlattenFunction;

    public function testFlattenWorksOnEmptyArray()
    {
        $this->assertEquals([], $this->flatten([]));
    }

    public function testFlattenReturnsExactAlreadyFlatResult()
    {
        $flatInput = [1, 2, 3, 4];
        $this->assertEquals($flatInput, $this->flatten($flatInput));
    }

    public function testFlattenIncludesNonFlatContentWithFlatContent()
    {
        $input = [1, 2, [3, 4, 5, [6, 7, 8], [9, 10]]];
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $this->flatten($input));
    }

    public function testThereAreNoTypeInferencesInFlattening()
    {
        $input = [true, 'false', [3.258, '4.14', -5, [0, false, null], [new StdClass(), 10]]];
        $this->assertEquals([true, 'false', 3.258, '4.14', -5, 0, false, null, new StdClass(), 10], $this->flatten($input));
    }
}
