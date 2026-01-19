<?php

namespace CrazyCodeGen\Rendering;

use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

class RenderingContext
{
    public function __construct(
        /** @var array<string> $importedClasses */
        public array    $importedClasses = [],
        /** @var ImportDef[] $importDefinitions */
        public array    $importDefinitions = [],
        public null|int $chopDownArraysAfterCharacters = null,
        public null|int $chopDownArrayIndentSize = null,
        public null|int $chopDownArrayHasTrailingCommaOnLastElement = null,
    ) {
    }

    public function addImportDef(ImportDef $importDef): void
    {
        $this->importDefinitions[] = $importDef;
    }

    public function getImportDef(ClassTypeDef $targetType): null|ImportDef
    {
        foreach ($this->importDefinitions as $importDef) {
            if ($importDef->type->type === $targetType->type) {
                return $importDef;
            }
        }
        return null;
    }

    public function getFinalName(ImportDef $importDefinition): string
    {
        if ($importDefinition->alias) {
            return $importDefinition->alias;
        }
        return $importDefinition->type->shortName;
    }
}
