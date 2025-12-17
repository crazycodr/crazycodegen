<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\UseTraitDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

trait HasTraitsTrait
{
    use ValidationTrait;

    /** @var UseTraitDef[] $traits */
    public array $traits = [];

    /**
     * @param array<string|ClassTypeDef|UseTraitDef> $traits
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setTraits(array $traits): static
    {
        $this->traits = [];
        foreach ($traits as $constant) {
            $this->addTrait($constant);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addTrait(string|ClassTypeDef|UseTraitDef $constant): static
    {
        $this->traits[] = $this->convertOrThrow($constant, [
            new ConversionRule(inputType: 'string', outputType: UseTraitDef::class),
            new ConversionRule(inputType: ClassTypeDef::class, outputType: UseTraitDef::class),
            new ConversionRule(inputType: UseTraitDef::class),
        ]);
        return $this;
    }
}
