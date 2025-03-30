<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

trait HasAbstractModifierTrait
{
    public bool $abstract = false;

    public function setAbstract(bool $isAbstract): self
    {
        $this->abstract = $isAbstract;
        return $this;
    }
}