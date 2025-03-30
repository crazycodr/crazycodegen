<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;

trait HasReturnTypeTrait
{
    use TypeInferenceTrait;

    public null|TypeDef $returnType = null;

    public function setReturnType(null|string|TypeDef $type): self
    {
        if (is_string($type)) {
            $type = $this->inferType($type);
        }
        $this->returnType = $type;
        return $this;
    }
}
