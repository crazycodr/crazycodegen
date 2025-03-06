<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

class ArgumentDefinitionRenderingRules
{
    public function __construct(
        public int $spacesBetweenArgumentTypeAndIdentifier = 1,
        public int $spacesBetweenArgumentIdentifierAndEquals = 1,
        public int $spacesBetweenArgumentEqualsAndValue = 1,
    )
    {

    }

    public function clone(): self
    {
        return clone $this;
    }
}