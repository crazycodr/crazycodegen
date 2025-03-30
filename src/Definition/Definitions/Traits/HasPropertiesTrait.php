<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;

trait HasPropertiesTrait
{
    use ValidationTrait;

    /** @var PropertyDef[] $properties */
    public array $properties = [];

    /**
     * @param string[]|PropertyDef[] $properties
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setProperties(array $properties): self
    {
        $this->properties = [];
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addProperty(string|PropertyDef $property): self
    {
        $this->properties[] = $this->convertOrThrow($property, [
            new ConversionRule(inputType: 'string', outputType: PropertyDef::class),
            new ConversionRule(inputType: PropertyDef::class),
        ]);
        return $this;
    }
}