<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\NamespaceDef;

trait HasNamespaceTrait
{
    use ValidationTrait;

    public null|NamespaceDef $namespace = null;

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function setNamespace(null|NamespaceDef $namespace): static
    {
        $this->namespace = $this->convertOrThrow($namespace, [
            new ConversionRule(inputType: 'null'),
            new ConversionRule(inputType: NamespaceDef::class),
        ]);
        return $this;
    }
}
