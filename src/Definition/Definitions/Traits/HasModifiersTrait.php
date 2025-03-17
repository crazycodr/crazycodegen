<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Enums\VisibilityEnum;
use CrazyCodeGen\Common\Traits\ValidationTrait;

trait HasModifiersTrait
{
    use ValidationTrait;

    public bool $abstract = false;
    public bool $static = false;
    public VisibilityEnum $visibility = VisibilityEnum::PUBLIC;

    public function setAbstract(bool $isAbstract): self
    {
        $this->abstract = $isAbstract;
        return $this;
    }

    public function setVisibility(VisibilityEnum $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }

    public function setStatic(bool $isStatic): self
    {
        $this->static = $isStatic;
        return $this;
    }
}