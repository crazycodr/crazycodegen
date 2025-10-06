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
     * @param null|DocBlockDef $docBlock
     * @return static
     * @throws NoValidConversionRulesMatchedException
     */
    public function setDocBlock(null|DocBlockDef $docBlock): static
    {
        $this->docBlock = $this->convertOrThrow($docBlock, rules: [
            new ConversionRule(inputType: 'null'),
            new ConversionRule(inputType: DocBlockDef::class),
        ]);
        return $this;
    }
}
