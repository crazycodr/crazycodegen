<?php

namespace CrazyCodeGen\Rendering\Renderers\Rules;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;

class RenderingRules
{
    public function __construct(
        public int               $lineLength = 120,
        public string            $indentation = '    ',
        public DocBlockRules     $docBlocks = new DocBlockRules(),
        public NamespaceRules    $namespaces = new NamespaceRules(),
        public ImportRules       $imports = new ImportRules(),
        public ArgumentListRules $argumentLists = new ArgumentListRules(),
        public ArgumentRules     $arguments = new ArgumentRules(),
        public FunctionRules     $functions = new FunctionRules(),
        public MethodRules       $methods = new MethodRules(),
        public ClassRules        $classes = new ClassRules(),
        public PropertyRules     $properties = new PropertyRules(),
        public ConditionRules    $conditions = new ConditionRules(),
    )
    {
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