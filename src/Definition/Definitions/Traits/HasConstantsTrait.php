<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\ConstantDef;

trait HasConstantsTrait
{
    use ValidationTrait;

    /** @var ConstantDef[] $constants */
    public array $constants = [];

    /**
     * @param string[]|ConstantDef[] $constants
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setConstants(array $constants): self
    {
        $this->constants = [];
        foreach ($constants as $constant) {
            $this->addConstant($constant);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addConstant(string|ConstantDef $constant): self
    {
        $this->constants[] = $this->convertOrThrow($constant, [
            new ConversionRule(inputType: 'string', outputType: ConstantDef::class),
            new ConversionRule(inputType: ConstantDef::class),
        ]);
        return $this;
    }
}
