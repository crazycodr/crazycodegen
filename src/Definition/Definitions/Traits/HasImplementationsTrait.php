<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

trait HasImplementationsTrait
{
    use ValidationTrait;

    /** @var ClassTypeDef[] $implementations */
    public array $implementations = [];

    /**
     * @param string[]|ClassTypeDef[] $implementations
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setImplementations(array $implementations): self
    {
        $this->implementations = [];
        foreach ($implementations as $implementation) {
            $this->addImplementation($implementation);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addImplementation(string|ClassTypeDef $implementation): self
    {
        $this->implementations[] = $this->convertOrThrow($implementation, [
            new ConversionRule(inputType: 'string', outputType: ClassTypeDef::class),
            new ConversionRule(inputType: ClassTypeDef::class),
        ]);
        return $this;
    }
}
