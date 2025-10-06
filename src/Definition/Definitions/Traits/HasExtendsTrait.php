<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

trait HasExtendsTrait
{
    use ValidationTrait;

    public null|ClassTypeDef $extends;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function setExtends(null|ClassTypeDef $extends): static
    {
        $this->extends = $this->convertOrThrow($extends, [
            new ConversionRule(inputType: 'null'),
            new ConversionRule(inputType: ClassTypeDef::class),
        ]);
        return $this;
    }
}
