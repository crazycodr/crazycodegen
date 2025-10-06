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
     * @param ClassTypeDef[] $implementations
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setImplementations(array $implementations): static
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
    public function addImplementation(ClassTypeDef $implementation): static
    {
        $this->implementations[] = $this->convertOrThrow($implementation, [
            new ConversionRule(inputType: ClassTypeDef::class),
        ]);
        return $this;
    }
}
