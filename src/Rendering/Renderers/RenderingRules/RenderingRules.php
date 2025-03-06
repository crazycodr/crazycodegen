<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

use CrazyCodeGen\Rendering\Renderers\ChopWrapDecisionEnum;
use CrazyCodeGen\Rendering\Renderers\RenderContext;
use CrazyCodeGen\Rendering\Renderers\BracePositionEnum;

class RenderingRules
{
    public function __construct(
        public int                                  $lineLength = 120,
        public string                               $indentation = '    ',
        public ChopWrapDecisionEnum                 $extendsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG,
        public ChopWrapDecisionEnum                 $implementsOnNextLine = ChopWrapDecisionEnum::CHOP_OR_WRAP_IF_TOO_LONG,
        public BracePositionEnum                    $classOpenBrace = BracePositionEnum::NEXT_LINE,
        public ArgumentListDefinitionRenderingRules $argumentListDefinitionRenderingRules = new ArgumentListDefinitionRenderingRules(),
        public ArgumentDefinitionRenderingRules     $argumentDefinitionRenderingRules = new ArgumentDefinitionRenderingRules(),
        public FunctionDefinitionRenderingRules     $functionDefinitionRenderingRules = new FunctionDefinitionRenderingRules(),
        public ClassDefinitionRenderingRules        $classDefinitionRenderingRules = new ClassDefinitionRenderingRules(),
        public int                                  $newLinesAfterNamespace = 2,
    )
    {

    }

    public function clone(): self
    {
        $clonedRules = clone $this;
        $clonedRules->argumentListDefinitionRenderingRules = $this->argumentListDefinitionRenderingRules->clone();
        $clonedRules->argumentDefinitionRenderingRules = $this->argumentDefinitionRenderingRules->clone();
        $clonedRules->functionDefinitionRenderingRules = $this->functionDefinitionRenderingRules->clone();
        return $clonedRules;
    }

    public function indent(RenderContext $context): void
    {
        $context->indents .= $this->indentation;
    }

    public function unindent(RenderContext $context): void
    {
        $context->indents = substr($context->indents, 0, -strlen($this->indentation));
    }

    public function exceedsAvailableSpace(string $existingContentLine, string $newContentText): bool
    {
        $newContentLines = explode("\n", $newContentText);
        if (empty($newContentLines)) {
            return true;
        }
        $newContentLine = $existingContentLine . $newContentLines[0];
        return strlen($newContentLine) > $this->lineLength;
    }
}