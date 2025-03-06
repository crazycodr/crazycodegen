<?php

namespace CrazyCodeGen\Rendering\Renderers\RenderingRules;

use CrazyCodeGen\Rendering\Renderers\Contexts\RenderContext;

class RenderingRules
{
    public function __construct(
        public int                          $lineLength = 120,
        public string                       $indentation = '    ',
        public NamespaceDeclarationRules    $namespaces = new NameSpaceDeclarationRules(),
        public ImportRules                  $imports = new ImportRules(),
        public ArgumentListDeclarationRules $argumentLists = new ArgumentListDeclarationRules(),
        public ArgumentDeclarationRules     $arguments = new ArgumentDeclarationRules(),
        public FunctionDefinitionRules      $functions = new FunctionDefinitionRules(),
        public MethodDefinitionRules        $methods = new MethodDefinitionRules(),
        public ClassDefinitionRules         $classes = new ClassDefinitionRules(),
        public PropertyDeclarationRules     $properties = new PropertyDeclarationRules(),
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