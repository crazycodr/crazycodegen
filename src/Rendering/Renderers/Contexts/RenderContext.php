<?php

namespace CrazyCodeGen\Rendering\Renderers\Contexts;

use CrazyCodeGen\Rendering\Renderers\Enums\ContextTypeEnum;

class RenderContext
{
    private string $currentLine = '';

    public function __construct(
        public string                 $buffer = '',
        public string                 $indents = '',
        /** @var ContextTypeEnum[] $contextStack */
        public array                  $contextStack = [],
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

    public function is(ContextTypeEnum $seekedContext): bool
    {
        return $this->contextStack[count($this->contextStack) - 1] === $seekedContext;
    }

    public function has(ContextTypeEnum $seekedContext): bool
    {
        return in_array($seekedContext, $this->contextStack);
    }
}