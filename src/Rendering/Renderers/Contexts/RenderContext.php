<?php

namespace CrazyCodeGen\Rendering\Renderers\Contexts;

class RenderContext
{
    private string $currentLine = '';

    public function __construct(
        public string                 $buffer = '',
        public string                 $indents = '',
        public int                    $argumentDefinitionTypePaddingSize = 0,
        public int                    $argumentDefinitionIdentifierPaddingSize = 0,
        public bool                   $forcedWrap = false,
        public bool                   $forcedChopDown = false,
        public ChopDownPaddingContext $chopDown = new ChopDownPaddingContext(),
        public array                  $importedClasses = [],
    )
    {
    }

    public function getCurrentLine(): string
    {
        return $this->currentLine;
    }
}