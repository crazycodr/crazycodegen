<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

trait HasStaticModifierTrait
{
    public bool $static = false;

    public function setStatic(bool $isStatic): self
    {
        $this->static = $isStatic;
        return $this;
    }
}
