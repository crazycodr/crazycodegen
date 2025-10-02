<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Definition\Definitions\Types\TypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeInferenceTrait;

trait HasReturnTypeTrait
{
    use TypeInferenceTrait;

    public null|TypeDef $returnType = null;

    public function setReturnType(null|TypeDef $type): self
    {
        $this->returnType = $type;
        return $this;
    }
}
