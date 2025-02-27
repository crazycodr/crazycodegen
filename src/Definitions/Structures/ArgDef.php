<?php

namespace CrazyCodeGen\Definitions\Structures;

use CrazyCodeGen\Base\CanBeAssigned;
use CrazyCodeGen\Base\CanBeComputed;
use CrazyCodeGen\Renderers\ContextShift;
use CrazyCodeGen\Renderers\ContextTypeEnum;
use CrazyCodeGen\Traits\ComputableTrait;

class ArgDef implements CanBeAssigned, CanBeComputed
{
    use ComputableTrait;

    public function __construct(
        public string      $name,
        public null|string $type = null,
        public null|string $defaultValue = null,
        public bool        $defaultValueIsNull = false,
    )
    {
    }

    public function getTokens(): array
    {
        $tokens = [];
        $tokens[] = ContextShift::push(ContextTypeEnum::argument);
        if ($this->type) {
            $tokens[] = ContextShift::push(ContextTypeEnum::type);
            $tokens[] = $this->type;
            $tokens[] = ContextShift::pop(ContextTypeEnum::type);
        }
        $tokens[] = '$' . $this->name;
        if ($this->defaultValue) {
            $tokens[] = '=';
            $tokens[] = $this->makeComputed($this->defaultValue)->getTokens();
        } elseif ($this->defaultValueIsNull) {
            $tokens[] = '=';
            $tokens[] = null;
        }
        $tokens[] = ContextShift::pop(ContextTypeEnum::argument);
        return $tokens;
    }
}
