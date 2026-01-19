<?php

namespace CrazyCodeGen\Formatting;

use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;
use CrazyCodeGen\Definition\Definitions\Types\TypeDef;

class FormattingContext
{
    /**
     * @var array<string, ImportDef> $importDefs
     */
    private array $importDefs = [];

    public function __construct(
        /**
         * @var array<ImportDef> $importDefs
         */
        array $importDefs = [],
    ) {
        $this->addImportDefs(...$importDefs);
    }

    public function addImportDefs(ImportDef ...$importDefs): void
    {
        foreach ($importDefs as $importDef) {
            $this->importDefs[$importDef->alias ?? $importDef->type->type] = $importDef;
        }
    }

    public function hasImportDefForType(null|TypeDef $type): bool
    {
        if ($type === null) {
            return false;
        }
        foreach ($this->importDefs as $importDef) {
            if (!$type instanceof ClassTypeDef) {
                continue;
            }
            if (($importDef->alias ?? $importDef->type->type) === $type->type) {
                return true;
            }
        }
        return false;
    }
}
