<?php

namespace CrazyCodeGen\Definition\Definitions\Types;

use CrazyCodeGen\Definition\Base\ShouldBeAccessedStatically;
use CrazyCodeGen\Definition\Base\ProvidesClassReference;
use CrazyCodeGen\Definition\Definitions\Values\ClassRefVal;
use CrazyCodeGen\Rendering\RenderingContext;
use CrazyCodeGen\Rendering\Tokens\KeywordTokens\SelfToken;
use CrazyCodeGen\Rendering\Tokens\Token;

class SelfTypeSpec extends TypeDef implements ShouldBeAccessedStatically, ProvidesClassReference
{
    /**
     * @return Token[]
     */
    public function getTokens(RenderingContext $context): array
    {
        return [new SelfToken()];
    }

    public function isAccessedStatically(): bool
    {
        return true;
    }

    public function getClassReference(): ClassRefVal
    {
        return new ClassRefVal($this);
    }
}
