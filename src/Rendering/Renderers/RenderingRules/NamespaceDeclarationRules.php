<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

class NamespaceDeclarationRules
{
    public function __construct(
        public int $spacesBetweenNamespaceTokenAndPath = 1,
        public int $linesAfterNamespaceDeclaration = 2,
    )
    {

    }
}