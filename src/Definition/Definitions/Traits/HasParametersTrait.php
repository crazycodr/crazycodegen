<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDef;
use CrazyCodeGen\Definition\Definitions\Structures\PropertyDef;

trait HasParametersTrait
{
    use ValidationTrait;

    public null|ParameterListDef $parameters = null;

    /**
     * @param array<PropertyDef|ParameterDef> $parameters
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setParameters(array $parameters, bool $allowProperties = false): static
    {
        if ($this->parameters === null) {
            $this->parameters = new ParameterListDef();
        }
        foreach ($parameters as $instruction) {
            $this->addParameter($instruction, allowProperties: $allowProperties);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addParameter(PropertyDef|ParameterDef $parameter, bool $allowProperties = false): static
    {
        if ($this->parameters === null) {
            $this->parameters = new ParameterListDef();
        }
        $rules = [];
        if ($allowProperties) {
            $rules[] = new ConversionRule(inputType: PropertyDef::class);
        }
        $rules[] = new ConversionRule(inputType: ParameterDef::class);
        $this->parameters->parameters[] = $this->convertOrThrow($parameter, $rules);
        return $this;
    }
}
