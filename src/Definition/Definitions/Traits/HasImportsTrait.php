<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Definition\Definitions\Structures\ImportFunctionDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

trait HasImportsTrait
{
    use ValidationTrait;

    /** @var array<ImportDef|ImportFunctionDef> $imports */
    public array $imports = [];

    /**
     * @param array<ClassTypeDef|ImportDef|ImportFunctionDef> $imports
     *
     * @throws NoValidConversionRulesMatchedException
     */
    public function setImports(array $imports): static
    {
        $this->imports = [];
        foreach ($imports as $import) {
            $this->addImport($import);
        }
        return $this;
    }

    /**
     * @throws NoValidConversionRulesMatchedException
     */
    public function addImport(ClassTypeDef|ImportFunctionDef|ImportDef $import): static
    {
        $this->imports[] = $this->convertOrThrow($import, [
            new ConversionRule(inputType: ClassTypeDef::class, outputType: ImportDef::class, propertyPaths: ['type']),
            new ConversionRule(inputType: ImportDef::class),
            new ConversionRule(inputType: ImportFunctionDef::class),
        ]);
        return $this;
    }
}
