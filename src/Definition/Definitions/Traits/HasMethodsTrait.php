<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\MethodDef;

trait HasMethodsTrait
{
    use ValidationTrait;

    /** @var MethodDef[] $methods */
    public array $methods = [];

    /**
     * @param string[]|MethodDef[] $methods
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setMethods(array $methods): static
    {
        $this->methods = [];
        foreach ($methods as $method) {
            $this->addMethod($method);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addMethod(string|MethodDef $method): static
    {
        $this->methods[] = $this->convertOrThrow($method, [
            new ConversionRule(inputType: 'string', outputType: MethodDef::class),
            new ConversionRule(inputType: MethodDef::class),
        ]);
        return $this;
    }
}
