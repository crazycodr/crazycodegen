<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

class ArgumentDeclarationRules
{
    public function __construct(
        public int $spacesBetweenArgumentTypeAndIdentifier = 1,
        public int $spacesBetweenArgumentIdentifierAndEquals = 1,
        public int $spacesBetweenArgumentEqualsAndValue = 1,
    )
    {
    }
}