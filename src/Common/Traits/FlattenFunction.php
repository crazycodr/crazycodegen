<?php

namespace CrazyCodeGen\Common\Traits;

trait FlattenFunction
{
    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    public function flatten(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten($value));
            } else {
                $result = array_merge($result, [$key => $value]);
            }
        }
        return $result;
    }
}
