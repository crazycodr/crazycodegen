<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\ImportDef;
use CrazyCodeGen\Definition\Definitions\Types\ClassTypeDef;

trait HasImportsTrait
{
    use ValidationTrait;

    /** @var ImportDef[] $imports */
    public array $imports = [];

    /**
     * @param ClassTypeDef[]|ImportDef[] $imports
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
    public function addImport(ClassTypeDef|ImportDef $import): static
    {
        $this->imports[] = $this->convertOrThrow($import, [
            new ConversionRule(inputType: ClassTypeDef::class, outputType: ImportDef::class, propertyPaths: ['type']),
            new ConversionRule(inputType: ImportDef::class),
        ]);
        return $this;
    }
}
