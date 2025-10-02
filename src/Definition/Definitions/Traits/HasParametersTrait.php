<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterDef;
use CrazyCodeGen\Definition\Definitions\Structures\ParameterListDef;

trait HasParametersTrait
{
    use ValidationTrait;

    public null|ParameterListDef $parameters = null;

    /**
     * @param string[]|ParameterDef[] $parameters
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setParameters(array $parameters): static
    {
        if ($this->parameters === null) {
            $this->parameters = new ParameterListDef();
        }
        foreach ($parameters as $instruction) {
            $this->addParameter($instruction);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addParameter(string|ParameterDef $parameter): static
    {
        if ($this->parameters === null) {
            $this->parameters = new ParameterListDef();
        }
        $this->parameters->parameters[] = $this->convertOrThrow($parameter, [
            new ConversionRule(inputType: 'string', outputType: ParameterDef::class),
            new ConversionRule(inputType: ParameterDef::class),
        ]);
        return $this;
    }
}
