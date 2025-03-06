<?php

namespace CrazyCodeGen\Definition\Traits;

use CrazyCodeGen\Definition\Base\CanBeCalled;

trait CallableTrait
{
    public function getCallReference(CanBeCalled|string $value): string
    {
        if ($value instanceof CanBeCalled) {
            return $value->getCallReference();
        }
        return $value;
    }
}
