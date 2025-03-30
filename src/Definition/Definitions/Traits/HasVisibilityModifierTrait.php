<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Enums\VisibilityEnum;

trait HasVisibilityModifierTrait
{
    public VisibilityEnum $visibility = VisibilityEnum::PUBLIC;

    public function setVisibility(VisibilityEnum $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }
}