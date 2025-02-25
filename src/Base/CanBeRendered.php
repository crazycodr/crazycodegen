<?php

namespace CrazyCodeGen\Base;

interface CanBeRendered
{
    /**
     * @return CanBeRendered[]|array
     */
    public function getTokens(): array;
}
