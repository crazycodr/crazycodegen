<?php

namespace CrazyCodeGen\Definition\Definitions\Traits;

use CrazyCodeGen\Common\Exceptions\NoValidConversionRulesMatchedException;
use CrazyCodeGen\Common\Models\ConversionRule;
use CrazyCodeGen\Common\Traits\ValidationTrait;
use CrazyCodeGen\Definition\Definitions\Structures\DocBlockDef;

trait HasDocBlockTrait
{
    use ValidationTrait;

    public null|DocBlockDef $docBlock = null;

    /**
     * @param string|string[]|DocBlockDef $docBlock
     * @throws NoValidConversionRulesMatchedException
     */
    public function setDocBlock(null|string|array|DocBlockDef $docBlock): self
    {
        $this->docBlock = $this->convertOrThrow($docBlock, rules: [
            new ConversionRule(inputType: 'null'),
            new ConversionRule(inputType: 'string', outputType: DocBlockDef::class),
            new ConversionRule(inputType: 'array', outputType: DocBlockDef::class, filter: fn($value) => is_string($value)),
            new ConversionRule(inputType: DocBlockDef::class),
        ]);
        return $this;
    }
}